<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité Location - Gestion des localisations des chambres
 * 
 * AMÉLIORATIONS APPORTÉES (Lawrence + Assistant) :
 * - Ajout du champ address (en anglais) pour la recherche avancée
 * - Correction de la relation avec Room (mappedBy: 'location')
 * - Support de la recherche par adresse complète
 * 
 * CHAMPS DISPONIBLES :
 * - city : Ville
 * - department : Département
 * - state : État/Région
 * - number : Numéro
 * - address : Adresse complète (AJOUTÉ)
 */
#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $department = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $number = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $state = null;

    /**
     * Adresse complète de la localisation
     * 
     * AJOUTÉ pour améliorer la recherche avancée
     * Permet de rechercher par adresse complète dans RoomRepository
     * 
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    /**
     * @var Collection<int, Room>
     */
    #[ORM\OneToMany(targetEntity: Room::class, mappedBy: 'location')]
    private Collection $rooms;

    public function __construct()
    {
        $this->rooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): static
    {
        $this->department = $department;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

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
            $room->setLocation($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): static
    {
        if ($this->rooms->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getLocation() === $this) {
                $room->setLocation(null);
            }
        }

        return $this;
    }
}
