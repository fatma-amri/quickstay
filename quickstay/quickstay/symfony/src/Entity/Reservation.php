<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservations')]
#[ORM\HasLifecycleCallbacks]
class Reservation
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $reference = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de début est obligatoire.')]
    #[Assert\GreaterThanOrEqual('today', message: 'La date de début doit être aujourd\'hui ou plus tard.')]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de fin est obligatoire.')]
    #[Assert\GreaterThan(propertyPath: 'startDate', message: 'La date de fin doit être après la date de début.')]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'Le nombre de personnes doit être positif.')]
    private int $guests = 1;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $specialRequests = null;

    #[ORM\Column(length: 20)]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $pricePerNight = null;

    #[ORM\Column]
    private int $nights = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $serviceFee = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $cleaningFee = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $cancellationReason = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $cancelledAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $confirmedAt = null;

    // Relations ManyToOne
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'L\'utilisateur est obligatoire.')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Property::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'La propriété est obligatoire.')]
    private ?Property $property = null;

    // Relation OneToOne vers Payment
    #[ORM\OneToOne(targetEntity: Payment::class, mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    // Relation OneToOne vers Review (un seul avis par réservation)
    #[ORM\OneToOne(targetEntity: Review::class, mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private ?Review $review = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->reference = $this->generateReference();
    }

    private function generateReference(): string
    {
        return 'QS-' . strtoupper(uniqid()) . '-' . date('Ymd');
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->createdAt = new \DateTime();
        if (!$this->reference) {
            $this->reference = $this->generateReference();
        }
        $this->calculatePricing();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function calculatePricing(): void
    {
        if ($this->startDate && $this->endDate && $this->property) {
            $this->nights = $this->startDate->diff($this->endDate)->days;
            $this->pricePerNight = $this->property->getPrice();
            
            $basePrice = (float) $this->pricePerNight * $this->nights;
            $serviceFee = (float) ($this->serviceFee ?? 0);
            $cleaningFee = (float) ($this->cleaningFee ?? 0);
            
            $this->totalPrice = (string) ($basePrice + $serviceFee + $cleaningFee);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getGuests(): int
    {
        return $this->guests;
    }

    public function setGuests(int $guests): static
    {
        $this->guests = $guests;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getSpecialRequests(): ?string
    {
        return $this->specialRequests;
    }

    public function setSpecialRequests(?string $specialRequests): static
    {
        $this->specialRequests = $specialRequests;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_CONFIRMED => 'Confirmée',
            self::STATUS_CANCELLED => 'Annulée',
            self::STATUS_COMPLETED => 'Terminée',
            self::STATUS_REJECTED => 'Rejetée',
            default => $this->status,
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'success',
            self::STATUS_CANCELLED => 'secondary',
            self::STATUS_COMPLETED => 'info',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary',
        };
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function confirm(): static
    {
        $this->status = self::STATUS_CONFIRMED;
        $this->confirmedAt = new \DateTime();
        return $this;
    }

    public function cancel(?string $reason = null): static
    {
        $this->status = self::STATUS_CANCELLED;
        $this->cancellationReason = $reason;
        $this->cancelledAt = new \DateTime();
        return $this;
    }

    public function complete(): static
    {
        $this->status = self::STATUS_COMPLETED;
        return $this;
    }

    public function reject(?string $reason = null): static
    {
        $this->status = self::STATUS_REJECTED;
        $this->cancellationReason = $reason;
        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): static
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getTotalPriceAsFloat(): float
    {
        return (float) $this->totalPrice;
    }

    public function getPricePerNight(): ?string
    {
        return $this->pricePerNight;
    }

    public function setPricePerNight(string $pricePerNight): static
    {
        $this->pricePerNight = $pricePerNight;
        return $this;
    }

    public function getNights(): int
    {
        return $this->nights;
    }

    public function setNights(int $nights): static
    {
        $this->nights = $nights;
        return $this;
    }

    public function getServiceFee(): ?string
    {
        return $this->serviceFee;
    }

    public function setServiceFee(?string $serviceFee): static
    {
        $this->serviceFee = $serviceFee;
        return $this;
    }

    public function getCleaningFee(): ?string
    {
        return $this->cleaningFee;
    }

    public function setCleaningFee(?string $cleaningFee): static
    {
        $this->cleaningFee = $cleaningFee;
        return $this;
    }

    public function getCancellationReason(): ?string
    {
        return $this->cancellationReason;
    }

    public function setCancellationReason(?string $cancellationReason): static
    {
        $this->cancellationReason = $cancellationReason;
        return $this;
    }

    public function getCancelledAt(): ?\DateTimeInterface
    {
        return $this->cancelledAt;
    }

    public function setCancelledAt(?\DateTimeInterface $cancelledAt): static
    {
        $this->cancelledAt = $cancelledAt;
        return $this;
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

    public function getConfirmedAt(): ?\DateTimeInterface
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?\DateTimeInterface $confirmedAt): static
    {
        $this->confirmedAt = $confirmedAt;
        return $this;
    }

    // Relations

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
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

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): static
    {
        if ($payment === null && $this->payment !== null) {
            $this->payment->setReservation(null);
        }

        if ($payment !== null && $payment->getReservation() !== $this) {
            $payment->setReservation($this);
        }

        $this->payment = $payment;
        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): static
    {
        if ($review === null && $this->review !== null) {
            $this->review->setReservation(null);
        }

        if ($review !== null && $review->getReservation() !== $this) {
            $review->setReservation($this);
        }

        $this->review = $review;
        return $this;
    }

    public function hasReview(): bool
    {
        return $this->review !== null;
    }

    public function __toString(): string
    {
        return sprintf('Réservation #%s', $this->reference ?? $this->id);
    }
}
