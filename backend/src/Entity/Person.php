<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\Table(name: 'person')]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: Employment::class, cascade: ['persist', 'remove'])]
    private Collection $employments;

    public function __construct()
    {
        $this->employments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): static
    {
        $today = new \DateTime();
        $interval = $today->diff($dateNaissance);
        if ($interval->y >= 150) {
            throw new \InvalidArgumentException('La personne ne peut pas avoir plus de 150ans.');
        }
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    /**
     * @return Collection<int, Employment>
     */
    public function getEmployments(): Collection
    {
        return $this->employments;
    }

    public function addEmployment(Employment $employment): static
    {
        if (!$this->employments->contains($employment)) {
            $this->employments->add($employment);
            $employment->setPerson($this);
        }
        return $this;
    }

    public function removeEmployment(Employment $employment): static
    {
        if ($this->employments->removeElement($employment)) {
            if ($employment->getPerson() === $this) {
                $employment->setPerson(null);
            }
        }
        return $this;
    }
}