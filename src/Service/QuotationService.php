<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Quotation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Twig\Environment;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Service de gestion des devis
 * 
 * Ce service gère :
 * - La création de devis à partir de réservations
 * - L'envoi de devis par email avec PDF joint
 * - La génération de PDF avec DomPDF
 * - Le suivi des statuts des devis
 * - Les statistiques des devis
 * 
 * Dépendances requises :
 * - composer require dompdf/dompdf (pour la génération PDF)
 * - Symfony Mailer (pour l'envoi d'emails)
 * - Twig (pour les templates)
 */
class QuotationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
        private Environment $twig
    ) {}

    /**
     * Crée un devis à partir d'une réservation
     * 
     * Cette méthode :
     * 1. Crée une nouvelle entité Quotation
     * 2. Calcule automatiquement le prix total
     * 3. Génère une description automatique
     * 4. Définit les conditions par défaut
     * 5. Sauvegarde en base de données
     * 
     * @param Booking $booking La réservation pour laquelle créer le devis
     * @param User $createdBy L'utilisateur qui crée le devis
     * @return Quotation Le devis créé
     */
    public function createFromBooking(Booking $booking, User $createdBy): Quotation
    {
        $quotation = new Quotation();
        $quotation->setBooking($booking);
        $quotation->setCreatedBy($createdBy);

        // Calculer le prix basé sur la chambre et la durée
        $price = $this->calculatePrice($booking);
        $quotation->setPrice($price);

        // Définir la description
        $description = $this->generateDescription($booking);
        $quotation->setDescription($description);

        // Définir les conditions
        $quotation->setTerms($this->getDefaultTerms());

        $this->em->persist($quotation);
        $this->em->flush();

        return $quotation;
    }

    /**
     * Calcule le prix total de la réservation
     * 
     * Calcul basé sur :
     * - Prix de la chambre par jour × nombre de jours
     * - Prix des options sélectionnées
     * 
     * @param Booking $booking La réservation
     * @return float Le prix total calculé
     */
    private function calculatePrice(Booking $booking): float
    {
        $room = $booking->getRoom();
        $basePrice = $room->getPrice() ?? 0; // Prix par jour de la chambre

        // Calculer la durée en jours
        $startDate = $booking->getStartDate();
        $endDate = $booking->getEndDate();
        $duration = $startDate->diff($endDate)->days;

        // Prix de base par jour
        $totalPrice = $basePrice * $duration;

        // Ajouter les options si présentes
        foreach ($booking->getOptions() as $option) {
            $totalPrice += $option->getPrice() ?? 0;
        }

        return $totalPrice;
    }

    /**
     * Génère une description automatique du devis
     * 
     * @param Booking $booking La réservation
     * @return string La description générée
     */
    private function generateDescription(Booking $booking): string
    {
        $room = $booking->getRoom();
        $startDate = $booking->getStartDate()->format('d/m/Y');
        $endDate = $booking->getEndDate()->format('d/m/Y');

        $description = "Réservation de la salle \"{$room->getName()}\" ";
        $description .= "du {$startDate} au {$endDate}. ";
        $description .= "Capacité : {$room->getCapacity()} personnes. ";

        if ($room->getDescription()) {
            $description .= "\n\nDescription : {$room->getDescription()}";
        }

        return $description;
    }

    /**
     * Conditions par défaut du devis
     * 
     * @return string Les conditions générales
     */
    private function getDefaultTerms(): string
    {
        return "Conditions générales :
        
1. Ce devis est valable 30 jours à compter de sa date d'émission
2. Une confirmation écrite est requise pour confirmer la réservation
3. Un acompte de 30% est exigé à la confirmation
4. Le solde doit être réglé 7 jours avant l'événement
5. Annulation possible jusqu'à 48h avant l'événement
6. Les équipements inclus sont ceux mentionnés dans la description
7. Toute modification doit être confirmée par écrit";
    }

    /**
     * Envoie le devis par email au client
     * 
     * Cette méthode :
     * 1. Génère le PDF du devis
     * 2. Crée l'email avec le PDF en pièce jointe
     * 3. Envoie l'email au client
     * 4. Met à jour le statut du devis
     * 
     * @param Quotation $quotation Le devis à envoyer
     * @return bool True si l'envoi a réussi, false sinon
     */
    public function sendQuotation(Quotation $quotation): bool
    {
        try {
            $booking = $quotation->getBooking();
            $client = $booking->getClient();
            $user = $booking->getUser();

            // Générer le PDF
            $pdfContent = $this->generatePdf($quotation);

            // Créer l'email
            $email = (new Email())
                ->from(new Address('noreply@reservation.com', 'Service Réservation'))
                ->to(new Address($user->getEmail(), $client->getName()))
                ->subject('Devis - ' . $quotation->getReference())
                ->html($this->generateEmailContent($quotation))
                ->attach($pdfContent, 'devis-' . $quotation->getReference() . '.pdf', 'application/pdf');

            $this->mailer->send($email);

            // Mettre à jour le statut
            $quotation->setStatus('sent');
            $this->em->flush();

            return true;
        } catch (\Exception $e) {
            // Log l'erreur
            error_log('Erreur envoi devis: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère le contenu HTML de l'email
     * 
     * @param Quotation $quotation Le devis
     * @return string Le contenu HTML de l'email
     */
    private function generateEmailContent(Quotation $quotation): string
    {
        $booking = $quotation->getBooking();
        $client = $booking->getClient();
        $room = $booking->getRoom();

        return $this->twig->render('emails/quotation.html.twig', [
            'quotation' => $quotation,
            'booking' => $booking,
            'client' => $client,
            'room' => $room
        ]);
    }

    /**
     * Génère un PDF du devis
     * 
     * Utilise DomPDF pour convertir le template Twig en PDF
     * 
     * Installation requise : composer require dompdf/dompdf
     * 
     * @param Quotation $quotation Le devis
     * @return string Le contenu PDF en binaire
     */
    public function generatePdf(Quotation $quotation): string
    {
        // Vérifier que DomPDF est installé
        if (!class_exists('Dompdf\Dompdf')) {
            throw new \RuntimeException(
                'DomPDF n\'est pas installé. Exécutez : composer require dompdf/dompdf'
            );
        }

        // Configuration de DomPDF
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);

        // Générer le HTML
        $html = $this->twig->render('pdf/quotation.html.twig', [
            'quotation' => $quotation,
            'booking' => $quotation->getBooking(),
            'client' => $quotation->getBooking()->getClient(),
            'room' => $quotation->getBooking()->getRoom()
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Marque un devis comme accepté
     * 
     * @param Quotation $quotation Le devis à accepter
     */
    public function acceptQuotation(Quotation $quotation): void
    {
        $quotation->setStatus('accepted');
        $this->em->flush();
    }

    /**
     * Marque un devis comme refusé
     * 
     * @param Quotation $quotation Le devis à refuser
     */
    public function rejectQuotation(Quotation $quotation): void
    {
        $quotation->setStatus('rejected');
        $this->em->flush();
    }

    /**
     * Vérifie et marque les devis expirés
     * 
     * Cette méthode peut être appelée par une tâche cron
     * pour automatiser la gestion des devis expirés
     * 
     * @return int Le nombre de devis marqués comme expirés
     */
    public function checkExpiredQuotations(): int
    {
        $expiredQuotations = $this->em->getRepository(Quotation::class)
            ->createQueryBuilder('q')
            ->where('q.validUntil < :now')
            ->andWhere('q.status IN (:statuses)')
            ->setParameter('now', new \DateTime())
            ->setParameter('statuses', ['draft', 'sent'])
            ->getQuery()
            ->getResult();

        $count = 0;
        foreach ($expiredQuotations as $quotation) {
            $quotation->setStatus('expired');
            $count++;
        }

        $this->em->flush();
        return $count;
    }

    /**
     * Récupère les statistiques des devis
     * 
     * @return array Les statistiques par statut
     */
    public function getQuotationStats(): array
    {
        $qb = $this->em->getRepository(Quotation::class)->createQueryBuilder('q');

        $stats = [
            'total' => $qb->select('COUNT(q.id)')->getQuery()->getSingleScalarResult(),
            'draft' => $qb->select('COUNT(q.id)')->where('q.status = :status')->setParameter('status', 'draft')->getQuery()->getSingleScalarResult(),
            'sent' => $qb->select('COUNT(q.id)')->where('q.status = :status')->setParameter('status', 'sent')->getQuery()->getSingleScalarResult(),
            'accepted' => $qb->select('COUNT(q.id)')->where('q.status = :status')->setParameter('status', 'accepted')->getQuery()->getSingleScalarResult(),
            'rejected' => $qb->select('COUNT(q.id)')->where('q.status = :status')->setParameter('status', 'rejected')->getQuery()->getSingleScalarResult(),
            'expired' => $qb->select('COUNT(q.id)')->where('q.status = :status')->setParameter('status', 'expired')->getQuery()->getSingleScalarResult(),
        ];

        return $stats;
    }
}
