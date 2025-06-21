<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entité Client - Gestion des clients du système
 * 
 * AMÉLIORATIONS APPORTÉES (Lawrence + Assistant) :
 * - Ajout du champ address (en anglais) pour la recherche avancée
 * - Support de la recherche par adresse du client
 * - Intégration dans le système de recherche multi-critères
 * 
 * RELATIONS :
 * - OneToMany avec Booking (mappedBy: 'client')
 * - OneToOne avec User (mappedBy: 'client')
 * 
 * CHAMPS DISPONIBLES :
 * - name : Nom du client
 * - address : Adresse du client (AJOUTÉ pour la recherche)
 */
#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $name = null;

    /**
     * Adresse du client
     * 
     * AJOUTÉ pour améliorer la recherche avancée
     * Permet de rechercher les chambres par adresse du client
     * dans RoomRepository (recherche par c.address)
     * 
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'client', orphanRemoval: true)]
    private Collection $bookings;

    #[ORM\OneToOne(mappedBy: 'client', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function __construct()
    {
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
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
            $booking->setClient($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getClient() === $this) {
                $booking->setClient(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setClient(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getClient() !== $this) {
            $user->setClient($this);
        }

        $this->user = $user;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
