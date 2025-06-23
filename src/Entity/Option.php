<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité Option - Gestion des options disponibles pour les chambres
 * 
 * CORRECTIONS APPORTÉES (Lawrence + Assistant) :
 * - Résolution du conflit de merge avec Hamza
 * - Fusion des deux méthodes __construct() en une seule
 * - Correction des relations mappedBy (option → options)
 * - Ajout des méthodes manquantes pour les bookings
 * 
 * RELATIONS :
 * - ManyToMany avec Room (inversedBy: 'options')
 * - ManyToMany avec Booking (mappedBy: 'options')
 */
#[ORM\Entity(repositoryClass: OptionRepository::class)]
class Option
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $name = null;

    /**
     * Prix de l'option
     * AJOUT : Champ prix pour le système de devis
     */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $price = null;

    /**
     * Relation ManyToMany avec Room
     * Une option peut être associée à plusieurs chambres
     * 
     * @var Collection<int, Room>
     */
    #[ORM\ManyToMany(targetEntity: Room::class, inversedBy: 'options')]
    private Collection $rooms;

    /**
     * Relation ManyToMany avec Booking
     * Une option peut être associée à plusieurs réservations
     * 
     * CORRECTION : mappedBy corrigé de 'option' vers 'options' (pluriel)
     * 
     * @var Collection<int, Booking>
     */
    #[ORM\ManyToMany(targetEntity: Booking::class, mappedBy: 'options')]
    private Collection $bookings;

    /**
     * Constructeur - Initialise les collections
     * 
     * CORRECTION : Fusion des deux constructeurs en un seul
     * pour éviter l'erreur "Cannot redeclare __construct()"
     */
    public function __construct()
    {
        $this->rooms = new ArrayCollection();
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

    /**
     * Récupère le prix de l'option
     * AJOUT : Méthode pour le système de devis
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * Définit le prix de l'option
     * AJOUT : Méthode pour le système de devis
     */
    public function setPrice(?float $price): static
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Récupère le prix formaté avec le symbole euro
     * AJOUT : Méthode pour l'affichage dans les templates
     */
    public function getFormattedPrice(): string
    {
        return number_format($this->price ?? 0, 2, ',', ' ') . ' €';
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): static
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms->add($room);
        }

        return $this;
    }

    public function removeRoom(Room $room): static
    {
        $this->rooms->removeElement($room);

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->addOption($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            $booking->removeOption($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
