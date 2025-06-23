<?php

namespace App\Entity;

use App\Repository\QuotationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[ORM\Entity(repositoryClass: QuotationRepository::class)]
#[HasLifecycleCallbacks]
class Quotation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $reference = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $price = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $taxRate = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $taxAmount = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $totalPrice = null;

    #[ORM\Column(length: 255)]
    private ?string $status = 'draft'; // draft, sent, accepted, rejected, expired

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $validUntil = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $terms = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'quotations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Booking $booking = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    public function __construct()
    {
        $this->status = 'draft';
        $this->taxRate = 20.0; // TVA par défaut
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Génère automatiquement une référence unique pour le devis
     */
    public function generateReference(): void
    {
        $this->reference = 'DEV-' . date('Y') . '-' . str_pad($this->id ?? 1, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Calcule le montant total avec taxes
     */
    public function calculateTotal(): void
    {
        $this->taxAmount = ($this->price * $this->taxRate) / 100;
        $this->totalPrice = $this->price + $this->taxAmount;
    }

    /**
     * Vérifie si le devis est encore valide
     */
    public function isValid(): bool
    {
        return $this->status !== 'expired' &&
            $this->validUntil &&
            $this->validUntil >= new \DateTime();
    }

    /**
     * Les évènements du cycle de vie de l'entité
     * La mise à jour des dates de création et de modification de l'entité
     */
    #[ORM\PrePersist] // Premier enregistrement d'un objet de l'entité
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();

        // Générer la référence si elle n'existe pas
        if (!$this->reference) {
            $this->generateReference();
        }

        // Calculer le total
        $this->calculateTotal();

        // Définir la date de validité par défaut (30 jours)
        if (!$this->validUntil) {
            $this->validUntil = new \DateTime('+30 days');
        }
    }

    #[ORM\PreUpdate] // Modification d'un objet de l'entité
    public function setUpdatedAtValue(): void
    {
        $this->updated_at = new \DateTimeImmutable();
        $this->calculateTotal();
    }

    // Récupère le nom de la salle via la propriété booking
    public function getRoom(): ?string
    {
        return $this->booking?->getRoom()?->getName();
    }

    // Récupère le nom du client via la propriété booking
    public function getClient(): ?string
    {
        return $this->booking?->getClient()?->getName();
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->price, 2, ',', ' ') . ' €';
    }

    public function getTaxRate(): ?float
    {
        return $this->taxRate;
    }

    public function setTaxRate(?float $taxRate): static
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    public function getTaxAmount(): ?float
    {
        return $this->taxAmount;
    }

    public function setTaxAmount(?float $taxAmount): static
    {
        $this->taxAmount = $taxAmount;
        return $this;
    }

    public function getFormattedTaxAmount(): string
    {
        return number_format($this->taxAmount ?? 0, 2, ',', ' ') . ' €';
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getFormattedTotalPrice(): string
    {
        return number_format($this->totalPrice, 2, ',', ' ') . ' €';
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
            'draft' => 'Brouillon',
            'sent' => 'Envoyé',
            'accepted' => 'Accepté',
            'rejected' => 'Refusé',
            'expired' => 'Expiré',
            default => 'Inconnu'
        };
    }

    public function getValidUntil(): ?\DateTimeInterface
    {
        return $this->validUntil;
    }

    public function setValidUntil(\DateTimeInterface $validUntil): static
    {
        $this->validUntil = $validUntil;
        return $this;
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

    public function getTerms(): ?string
    {
        return $this->terms;
    }

    public function setTerms(?string $terms): static
    {
        $this->terms = $terms;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): static
    {
        $this->booking = $booking;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }
}
