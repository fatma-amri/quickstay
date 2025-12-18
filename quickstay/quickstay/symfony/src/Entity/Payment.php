<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\Table(name: 'payments')]
#[ORM\HasLifecycleCallbacks]
class Payment
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_PARTIALLY_REFUNDED = 'partially_refunded';

    public const METHOD_CARD = 'card';
    public const METHOD_BANK_TRANSFER = 'bank_transfer';
    public const METHOD_CASH = 'cash';
    public const METHOD_PAYPAL = 'paypal';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $transactionId = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le montant est obligatoire.')]
    #[Assert\Positive(message: 'Le montant doit être positif.')]
    private ?string $amount = null;

    #[ORM\Column(length: 3)]
    private string $currency = 'TND';

    #[ORM\Column(length: 20)]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column(length: 50)]
    private ?string $method = self::METHOD_CARD;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    // Informations de carte (masquées)
    #[ORM\Column(length: 4, nullable: true)]
    private ?string $cardLastFour = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $cardBrand = null;

    // Informations de facturation
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $billingName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $billingEmail = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $billingAddress = null;

    // Remboursement
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $refundedAmount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $refundedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $refundReason = null;

    // Métadonnées
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $metadata = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $failureReason = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $gatewayReference = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $paidAt = null;

    // Relations
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToOne(targetEntity: Reservation::class, inversedBy: 'payment')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->transactionId = $this->generateTransactionId();
    }

    private function generateTransactionId(): string
    {
        return 'PAY-' . strtoupper(bin2hex(random_bytes(8))) . '-' . time();
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->createdAt = new \DateTime();
        if (!$this->transactionId) {
            $this->transactionId = $this->generateTransactionId();
        }
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(string $transactionId): static
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAmountAsFloat(): float
    {
        return (float) $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;
        return $this;
    }

    public function getFormattedAmount(): string
    {
        return number_format($this->getAmountAsFloat(), 2, ',', ' ') . ' ' . $this->currency;
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
            self::STATUS_PROCESSING => 'En cours',
            self::STATUS_COMPLETED => 'Complété',
            self::STATUS_FAILED => 'Échoué',
            self::STATUS_REFUNDED => 'Remboursé',
            self::STATUS_PARTIALLY_REFUNDED => 'Partiellement remboursé',
            default => $this->status,
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_FAILED => 'danger',
            self::STATUS_REFUNDED, self::STATUS_PARTIALLY_REFUNDED => 'secondary',
            default => 'secondary',
        };
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isRefunded(): bool
    {
        return in_array($this->status, [self::STATUS_REFUNDED, self::STATUS_PARTIALLY_REFUNDED]);
    }

    public function complete(): static
    {
        $this->status = self::STATUS_COMPLETED;
        $this->paidAt = new \DateTime();
        return $this;
    }

    public function fail(string $reason = null): static
    {
        $this->status = self::STATUS_FAILED;
        $this->failureReason = $reason;
        return $this;
    }

    public function refund(string $amount = null, string $reason = null): static
    {
        if ($amount === null || (float) $amount >= $this->getAmountAsFloat()) {
            $this->refundedAmount = $this->amount;
            $this->status = self::STATUS_REFUNDED;
        } else {
            $this->refundedAmount = $amount;
            $this->status = self::STATUS_PARTIALLY_REFUNDED;
        }
        $this->refundedAt = new \DateTime();
        $this->refundReason = $reason;
        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): static
    {
        $this->method = $method;
        return $this;
    }

    public function getMethodLabel(): string
    {
        return match ($this->method) {
            self::METHOD_CARD => 'Carte bancaire',
            self::METHOD_BANK_TRANSFER => 'Virement bancaire',
            self::METHOD_CASH => 'Espèces',
            self::METHOD_PAYPAL => 'PayPal',
            default => $this->method,
        };
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCardLastFour(): ?string
    {
        return $this->cardLastFour;
    }

    public function setCardLastFour(?string $cardLastFour): static
    {
        $this->cardLastFour = $cardLastFour;
        return $this;
    }

    public function getCardBrand(): ?string
    {
        return $this->cardBrand;
    }

    public function setCardBrand(?string $cardBrand): static
    {
        $this->cardBrand = $cardBrand;
        return $this;
    }

    public function getMaskedCard(): ?string
    {
        if (!$this->cardLastFour) {
            return null;
        }
        return '**** **** **** ' . $this->cardLastFour;
    }

    public function getBillingName(): ?string
    {
        return $this->billingName;
    }

    public function setBillingName(?string $billingName): static
    {
        $this->billingName = $billingName;
        return $this;
    }

    public function getBillingEmail(): ?string
    {
        return $this->billingEmail;
    }

    public function setBillingEmail(?string $billingEmail): static
    {
        $this->billingEmail = $billingEmail;
        return $this;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?string $billingAddress): static
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }

    public function getRefundedAmount(): ?string
    {
        return $this->refundedAmount;
    }

    public function setRefundedAmount(?string $refundedAmount): static
    {
        $this->refundedAmount = $refundedAmount;
        return $this;
    }

    public function getRefundedAt(): ?\DateTimeInterface
    {
        return $this->refundedAt;
    }

    public function setRefundedAt(?\DateTimeInterface $refundedAt): static
    {
        $this->refundedAt = $refundedAt;
        return $this;
    }

    public function getRefundReason(): ?string
    {
        return $this->refundReason;
    }

    public function setRefundReason(?string $refundReason): static
    {
        $this->refundReason = $refundReason;
        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): static
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function addMetadata(string $key, mixed $value): static
    {
        $this->metadata[$key] = $value;
        return $this;
    }

    public function getFailureReason(): ?string
    {
        return $this->failureReason;
    }

    public function setFailureReason(?string $failureReason): static
    {
        $this->failureReason = $failureReason;
        return $this;
    }

    public function getGatewayReference(): ?string
    {
        return $this->gatewayReference;
    }

    public function setGatewayReference(?string $gatewayReference): static
    {
        $this->gatewayReference = $gatewayReference;
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

    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paidAt;
    }

    public function setPaidAt(?\DateTimeInterface $paidAt): static
    {
        $this->paidAt = $paidAt;
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

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf('Paiement #%s - %s', $this->transactionId, $this->getFormattedAmount());
    }
}
