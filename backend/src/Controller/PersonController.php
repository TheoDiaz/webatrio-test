<?php

namespace App\Controller;

use App\Entity\Person;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/persons')]
class PersonController extends AbstractController
{
    private $entityManager;
    private $personRepository;
    private $serializer;
    private $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        PersonRepository $personRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->personRepository = $personRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $person = new Person();
        $person->setNom($data['nom'] ?? '');
        $person->setPrenom($data['prenom'] ?? '');
        $person->setDateNaissance(new \DateTime($data['dateNaissance'] ?? 'now'));

        $errors = $this->validator->validate($person);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $this->entityManager->persist($person);
        $this->entityManager->flush();

        $json = $this->serializer->serialize($person, 'json', ['groups' => 'person:read']);
        return new JsonResponse($json, 201, [], true);
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $persons = $this->personRepository->findBy([], ['nom' => 'ASC', 'prenom' => 'ASC']);
        $data = array_map(function (Person $person) {
            $today = new \DateTime();
            $age = $today->diff($person->getDateNaissance())->y;
            $currentEmployments = $person->getEmployments()->filter(
                fn($emp) => !$emp->getDateFin() || $emp->getDateFin() >= $today
            );
            $currentEmploymentsSerialized = $this->serializer->serialize(
                array_values($currentEmployments->toArray()),
                'json',
                [
                    'groups' => ['employment:read', 'person:read'],
                    'datetime_format' => 'Y-m-d',
                    'circular_reference_handler' => fn($object) => $object->getId()
                ]
            );
            return [
                'id' => $person->getId(),
                'nom' => $person->getNom(),
                'prenom' => $person->getPrenom(),
                'age' => $age,
                'currentEmployments' => json_decode($currentEmploymentsSerialized, true)
            ];
        }, $persons);

        return new JsonResponse($data, 200);
    }
}