<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

##[UniqueEntity('etudiant')]

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
// #[UniqueEntity(fields: ["etudiant"], message: "Une réservation est déja faite pour ce compte")]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_exam = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $amount = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $transaction_state = null;

    #[ORM\OneToOne(cascade: ['persist'])]
    private ?Etudiant $etudiant = null;

    #[ORM\Column(length: 10)]
    private ?string $type_permis = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?string $hours = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Instructeur $instructeurs = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateExam(): ?\DateTimeInterface
    {
        return $this->date_exam;
    }

    public function setDateExam(?\DateTimeInterface $date_exam): static
    {
        $this->date_exam = $date_exam;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getTransactionState(): ?string
    {
        return $this->transaction_state;
    }

    public function setTransactionState(?string $transaction_state): static
    {
        $this->transaction_state = $transaction_state;

        return $this;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getTypePermis(): ?string
    {
        return $this->type_permis;
    }

    public function setTypePermis(string $type_permis): static
    {
        $this->type_permis = $type_permis;

        return $this;
    }

    public function getHours(): ?string
    {
        return $this->hours;
    }

    public function setHours(string $hours): static
    {
        $this->hours = $hours;

        return $this;
    }

    public function getInstructeurs(): ?Instructeur
    {
        return $this->instructeurs;
    }

    public function setInstructeurs(?Instructeur $instructeurs): static
    {
        $this->instructeurs = $instructeurs;

        return $this;
    }

}
