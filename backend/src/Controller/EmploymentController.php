<?php

namespace App\Controller;

use App\Entity\Employment;
use App\Entity\Person;
use App\Repository\EmploymentRepository;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class EmploymentController extends AbstractController
{
    private $entityManager;
    private $employmentRepository;
    private $personRepository;
    private $serializer;
    private $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        EmploymentRepository $employmentRepository,
        PersonRepository $personRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->employmentRepository = $employmentRepository;
        $this->personRepository = $personRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    #[Route('/persons/{id}/employments', methods: ['POST'])]
    public function create(Request $request, int $id): JsonResponse
    {
        $person = $this->personRepository->find($id);
        if (!$person) {
            return $this->json(['error' => 'Person not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $employment = new Employment();
        $employment->setPerson($person);
        $employment->setNomEntreprise($data['nomEntreprise'] ?? '');
        $employment->setPoste($data['poste'] ?? '');
        $employment->setDateDebut(new \DateTime($data['dateDebut'] ?? 'now'));
        if (!empty($data['dateFin'])) {
            $employment->setDateFin(new \DateTime($data['dateFin']));
        }

        $errors = $this->validator->validate($employment);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $this->entityManager->persist($employment);
        $this->entityManager->flush();

        $json = $this->serializer->serialize($employment, 'json', ['groups' => 'employment:read']);
        return new JsonResponse($json, 201, [], true);
    }

    #[Route('/employments', methods: ['GET'])]
    public function listByCompany(Request $request, EmploymentRepository $employmentRepository, SerializerInterface $serializer): JsonResponse
    {
        $company = $request->query->get('company');
        if (!$company) {
            return $this->json(['error' => 'Company parameter is required'], 400);
        }

        $employments = $employmentRepository->findBy(['nomEntreprise' => $company]);
        $json = $serializer->serialize($employments, 'json', [
            'groups' => ['employment:read', 'person:read'],
            'datetime_format' => 'Y-m-d',
            'circular_reference_handler' => fn($object) => $object->getId()
        ]);

        return new JsonResponse(json_decode($json, true), 200);
    }

    #[Route('/companies', name: 'api_companies', methods: ['GET'])]
    public function getCompanies(EmploymentRepository $employmentRepository): JsonResponse
    {
        $companies = $employmentRepository->findDistinctCompanies();
        return new JsonResponse($companies, 200);
    }
}