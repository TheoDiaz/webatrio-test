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

        try {
            $person->setNom($data['nom'] ?? '');
            $person->setPrenom($data['prenom'] ?? '');
            $person->setDateNaissance(new \DateTime($data['dateNaissance'] ?? 'now'));
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid date format for dateNaissance, use YYYY-MM-DD'], 400);
        }

        $errors = $this->validator->validate($person);
        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], 400);
        }

        $this->entityManager->persist($person);
        $this->entityManager->flush();

        $json = $this->serializer->serialize($person, 'json', [
            'groups' => 'person:read',
            'datetime_format' => 'Y-m-d',
            'circular_reference_handler' => fn($object) => $object->getId()
        ]);
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
    #[Route('/{id}/employments/between-dates', methods: ['GET'])]
public function getEmploymentsBetweenDates(Request $request, int $id): JsonResponse
{
    // Récupérer les paramètres startDate et endDate depuis la requête
    $startDateStr = $request->query->get('startDate');
    $endDateStr = $request->query->get('endDate');

    // Valider la présence des paramètres
    if (!$startDateStr || !$endDateStr) {
        return $this->json(['error' => 'Les paramètres startDate et endDate sont requis'], 400);
    }

    // Convertir les dates en objets DateTime
    try {
        $startDate = new \DateTime($startDateStr);
        $endDate = new \DateTime($endDateStr);
    } catch (\Exception $e) {
        return $this->json(['error' => 'Format de date invalide, utilisez YYYY-MM-DD'], 400);
    }

    // Vérifier que startDate est antérieure à endDate
    if ($startDate > $endDate) {
        return $this->json(['error' => 'startDate doit être antérieure à endDate'], 400);
    }

    // Récupérer la personne
    $person = $this->personRepository->find($id);
    if (!$person) {
        return $this->json(['error' => 'Personne non trouvée'], 404);
    }

    // Filtrer les emplois entre les deux dates
    $employments = $person->getEmployments()->filter(function ($employment) use ($startDate, $endDate) {
        $dateDebut = $employment->getDateDebut();
        $dateFin = $employment->getDateFin() ?? new \DateTime(); // Si dateFin est null, considérer comme en cours

        // Vérifier si l'emploi chevauche la période spécifiée
        return ($dateDebut <= $endDate) && ($dateFin >= $startDate);
    });

    // Sérialiser les emplois
    $json = $this->serializer->serialize(
        array_values($employments->toArray()),
        'json',
        [
            'groups' => ['employment:read', 'person:read'],
            'datetime_format' => 'Y-m-d',
            'circular_reference_handler' => fn($object) => $object->getId()
        ]
    );

    return new JsonResponse($json, 200, [], true);
}
}