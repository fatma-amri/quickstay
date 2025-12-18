<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\Table(name: 'reviews')]
#[ORM\HasLifecycleCallbacks]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La note est obligatoire.')]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: 'La note doit être entre {{ min }} et {{ max }}.'
    )]
    private ?int $rating = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le commentaire est obligatoire.')]
    #[Assert\Length(
        min: 10,
        max: 2000,
        minMessage: 'Le commentaire doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le commentaire ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $comment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    // Notes détaillées
    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $cleanlinessRating = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $communicationRating = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $locationRating = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $valueRating = null;

    #[ORM\Column]
    private bool $isApproved = false;

    #[ORM\Column]
    private bool $isReported = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ownerResponse = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $ownerResponseAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    // Relations
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: Property::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Property $property = null;

    #[ORM\OneToOne(targetEntity: Reservation::class, inversedBy: 'review')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Reservation $reservation = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getCleanlinessRating(): ?int
    {
        return $this->cleanlinessRating;
    }

    public function setCleanlinessRating(?int $cleanlinessRating): static
    {
        $this->cleanlinessRating = $cleanlinessRating;
        return $this;
    }

    public function getCommunicationRating(): ?int
    {
        return $this->communicationRating;
    }

    public function setCommunicationRating(?int $communicationRating): static
    {
        $this->communicationRating = $communicationRating;
        return $this;
    }

    public function getLocationRating(): ?int
    {
        return $this->locationRating;
    }

    public function setLocationRating(?int $locationRating): static
    {
        $this->locationRating = $locationRating;
        return $this;
    }

    public function getValueRating(): ?int
    {
        return $this->valueRating;
    }

    public function setValueRating(?int $valueRating): static
    {
        $this->valueRating = $valueRating;
        return $this;
    }

    public function getAverageDetailedRating(): ?float
    {
        $ratings = array_filter([
            $this->cleanlinessRating,
            $this->communicationRating,
            $this->locationRating,
            $this->valueRating,
        ]);

        if (empty($ratings)) {
            return null;
        }

        return round(array_sum($ratings) / count($ratings), 1);
    }

    public function isApproved(): bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): static
    {
        $this->isApproved = $isApproved;
        return $this;
    }

    public function approve(): static
    {
        $this->isApproved = true;
        return $this;
    }

    public function reject(): static
    {
        $this->isApproved = false;
        return $this;
    }

    public function isReported(): bool
    {
        return $this->isReported;
    }

    public function setIsReported(bool $isReported): static
    {
        $this->isReported = $isReported;
        return $this;
    }

    public function report(): static
    {
        $this->isReported = true;
        return $this;
    }

    public function getOwnerResponse(): ?string
    {
        return $this->ownerResponse;
    }

    public function setOwnerResponse(?string $ownerResponse): static
    {
        $this->ownerResponse = $ownerResponse;
        if ($ownerResponse !== null) {
            $this->ownerResponseAt = new \DateTime();
        }
        return $this;
    }

    public function getOwnerResponseAt(): ?\DateTimeInterface
    {
        return $this->ownerResponseAt;
    }

    public function setOwnerResponseAt(?\DateTimeInterface $ownerResponseAt): static
    {
        $this->ownerResponseAt = $ownerResponseAt;
        return $this;
    }

    public function hasOwnerResponse(): bool
    {
        return $this->ownerResponse !== null;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // Relations

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;
        return $this;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): static
    {
        $this->property = $property;
        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;
        return $this;
    }

    public function getRatingStars(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function __toString(): string
    {
        return sprintf('Avis #%d - %d/5', $this->id, $this->rating);
    }
}
