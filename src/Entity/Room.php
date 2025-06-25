<?php

namespace App\Entity;

use App\Entity\Equipment;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RoomRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Inflector\Rules\Pattern;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isAvailable = null;

    /**
     * Prix de la chambre par jour
     * AJOUT : Champ prix pour le système de devis
     */
    #[ORM\Column(type: 'float')]
    private ?float $price = null;

    /**
     * Relation ManyToMany avec Equipment
     * Une chambre peut avoir plusieurs équipements
     * 
     * @var Collection<int, Equipment>
     */
    #[ORM\ManyToMany(targetEntity: Equipment::class, inversedBy: 'rooms')]
    private Collection $equipments;

    /**
     * Relation ManyToMany avec Option
     * Une chambre peut avoir plusieurs options
     * 
     * @var Collection<int, Option>
     */
    #[ORM\ManyToMany(targetEntity: Option::class, inversedBy: 'rooms')]
    private Collection $options;

    /**
     * @var Collection<int, Favorite>
     */
    #[ORM\OneToMany(targetEntity: Favorite::class, mappedBy: 'room')]
    private Collection $favorites;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'room', orphanRemoval: true)]
    private Collection $reviews;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'room')]
    private Collection $bookings;

    /**
     * Relation ManyToOne avec Location
     * Une chambre appartient à une localisation
     * 
     * AMÉLIORATION : Ajout de la colonne de jointure et correction de inversedBy
     * - inversedBy corrigé de 'room' vers 'rooms' (pluriel)
     * - Ajout de JoinColumn pour définir la colonne location_id
     * 
     * @var Location|null
     */
    #[ORM\ManyToOne(inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Location $location = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $luminosity = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $pmr_access = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $ergonomics_notes = null;

    public function __construct()
    {
        $this->favorites = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->options = new ArrayCollection();
        $this->equipments = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    /**
     * Récupère le prix de la chambre par jour
     * AJOUT : Méthode pour le système de devis
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * Définit le prix de la chambre par jour
     * AJOUT : Méthode pour le système de devis
     */
    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Retourne le prix formaté avec le symbole euro
     * AJOUT : Méthode pour l'affichage formaté
     */
    public function getFormattedPrice(): string
    {
        return number_format($this->price ?? 0, 2, ',', ' ') . ' €';
    }

    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): static
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->setRoom($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): static
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getRoom() === $this) {
                $favorite->setRoom(null);
            }
        }

        return $this;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setRoom($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getRoom() === $this) {
                $review->setRoom(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): static
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->addRoom($this);
        }

        return $this;
    }

    public function removeOption(Option $option): static
    {
        if ($this->options->removeElement($option)) {
            $option->removeRoom($this);
        }

        return $this;
    }

    public function addEquipment(Equipment $equipment): static
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments->add($equipment);
            $equipment->addRoom($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): static
    {
        if ($this->equipments->removeElement($equipment)) {
            $equipment->removeRoom($this);
        }

        return $this;
    }

    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setRoom($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getRoom() === $this) {
                $booking->setRoom(null);
            }
        }

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function hasLuminosity(): bool
    {
        return $this->luminosity;
    }

    public function setLuminosity(bool $luminosity): static
    {
        $this->luminosity = $luminosity;

        return $this;
    }

    public function hasPmrAccess(): bool
    {
        return $this->pmr_access;
    }

    public function setPmrAccess(bool $pmr_access): static
    {
        $this->pmr_access = $pmr_access;

        return $this;
    }

    public function getErgonomicsNotes(): ?string
    {
        return $this->ergonomics_notes;
    }

    public function setErgonomicsNotes(?string $notes): static
    {
        $this->ergonomics_notes = $notes;

        return $this;
    }
}
