<?php

namespace App\Entity;

use App\Repository\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
#[ORM\Table(name: 'properties')]
#[ORM\HasLifecycleCallbacks]
class Property
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    public const TYPE_APARTMENT = 'apartment';
    public const TYPE_HOUSE = 'house';
    public const TYPE_VILLA = 'villa';
    public const TYPE_STUDIO = 'studio';
    public const TYPE_LOFT = 'loft';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(
        min: 20,
        minMessage: 'La description doit contenir au moins {{ limit }} caractères.'
    )]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le prix est obligatoire.')]
    #[Assert\Positive(message: 'Le prix doit être positif.')]
    private ?string $price = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'adresse est obligatoire.')]
    private ?string $address = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'La ville est obligatoire.')]
    private ?string $city = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $country = 'Tunisie';

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: [self::TYPE_APARTMENT, self::TYPE_HOUSE, self::TYPE_VILLA, self::TYPE_STUDIO, self::TYPE_LOFT])]
    private ?string $type = self::TYPE_APARTMENT;

    #[ORM\Column]
    #[Assert\Positive(message: 'Le nombre de chambres doit être positif.')]
    private int $bedrooms = 1;

    #[ORM\Column]
    #[Assert\Positive(message: 'Le nombre de salles de bain doit être positif.')]
    private int $bathrooms = 1;

    #[ORM\Column]
    #[Assert\Positive(message: 'La capacité doit être positive.')]
    private int $capacity = 2;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $surface = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mainImage = null;

    #[ORM\Column(type: Types::JSON)]
    private array $images = [];

    #[ORM\Column(type: Types::JSON)]
    private array $amenities = [];

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: [self::STATUS_DRAFT, self::STATUS_PUBLISHED, self::STATUS_ARCHIVED])]
    private ?string $status = self::STATUS_DRAFT;

    #[ORM\Column]
    private bool $isAvailable = true;

    #[ORM\Column]
    private bool $isFeatured = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    // Relations
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'properties')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $owner = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'properties')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'property', orphanRemoval: true)]
    private Collection $reservations;

    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'property', orphanRemoval: true)]
    private Collection $reviews;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->reviews = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getPriceAsFloat(): float
    {
        return (float) $this->price;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getFullAddress(): string
    {
        $parts = array_filter([$this->address, $this->postalCode, $this->city, $this->country]);
        return implode(', ', $parts);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_APARTMENT => 'Appartement',
            self::TYPE_HOUSE => 'Maison',
            self::TYPE_VILLA => 'Villa',
            self::TYPE_STUDIO => 'Studio',
            self::TYPE_LOFT => 'Loft',
            default => $this->type,
        };
    }

    public function getBedrooms(): int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(int $bedrooms): static
    {
        $this->bedrooms = $bedrooms;
        return $this;
    }

    public function getBathrooms(): int
    {
        return $this->bathrooms;
    }

    public function setBathrooms(int $bathrooms): static
    {
        $this->bathrooms = $bathrooms;
        return $this;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;
        return $this;
    }

    public function getSurface(): ?string
    {
        return $this->surface;
    }

    public function setSurface(?string $surface): static
    {
        $this->surface = $surface;
        return $this;
    }

    public function getMainImage(): ?string
    {
        return $this->mainImage;
    }

    public function setMainImage(?string $mainImage): static
    {
        $this->mainImage = $mainImage;
        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): static
    {
        $this->images = $images;
        return $this;
    }

    public function addImage(string $image): static
    {
        if (!in_array($image, $this->images, true)) {
            $this->images[] = $image;
        }
        return $this;
    }

    public function removeImage(string $image): static
    {
        $this->images = array_values(array_filter($this->images, fn($i) => $i !== $image));
        return $this;
    }

    public function getAmenities(): array
    {
        return $this->amenities;
    }

    public function setAmenities(array $amenities): static
    {
        $this->amenities = $amenities;
        return $this;
    }

    public function hasAmenity(string $amenity): bool
    {
        return in_array($amenity, $this->amenities, true);
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

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;
        return $this;
    }

    public function isFeatured(): bool
    {
        return $this->isFeatured;
    }

    public function setIsFeatured(bool $isFeatured): static
    {
        $this->isFeatured = $isFeatured;
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

    // Relations

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setProperty($this);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getProperty() === $this) {
                $reservation->setProperty(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setProperty($this);
        }
        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getProperty() === $this) {
                $review->setProperty(null);
            }
        }
        return $this;
    }

    public function getAverageRating(): ?float
    {
        $reviews = $this->reviews->filter(fn(Review $r) => $r->isApproved());
        if ($reviews->isEmpty()) {
            return null;
        }
        $sum = array_reduce($reviews->toArray(), fn($carry, Review $r) => $carry + $r->getRating(), 0);
        return round($sum / $reviews->count(), 1);
    }

    public function getReviewsCount(): int
    {
        return $this->reviews->filter(fn(Review $r) => $r->isApproved())->count();
    }

    public function __toString(): string
    {
        return $this->title ?? '';
    }
}
