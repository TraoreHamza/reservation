<?php

namespace App\Service;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Service de gestion des notifications administrateur
 * 
 * FONCTIONNALITÉS :
 * - Alerte 5 jours avant réservation non validée
 * - Notifications sur le dashboard
 * - Gestion des statuts de réservation
 * - Notifications de rappel pour les réservations à venir
 */
class NotificationService
{
    public function __construct(
        private BookingRepository $bookingRepository,
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        private MailerInterface $mailer
    ) {}

    /**
     * Envoie une notification de rappel pour une réservation
     * 
     * @param Booking $booking La réservation concernée
     * @param int $minutesBefore Minutes avant le début de la réservation
     */
    public function sendBookingReminder(Booking $booking, int $minutesBefore = 10): void
    {
        $client = $booking->getClient();
        $room = $booking->getRoom();

        if (!$client || !$room) {
            return; // Impossible d'envoyer la notification sans client ou salle
        }

        // Récupérer l'email via l'utilisateur associé au client
        $user = $client->getUser();
        $emailAddress = $user?->getEmail() ?? 'admin@reservation.com';

        // Créer l'email de rappel
        $email = (new Email())
            ->from('noreply@reservation.com')
            ->to($emailAddress)
            ->subject('Rappel : Votre réservation dans ' . $minutesBefore . ' minutes')
            ->html($this->generateReminderEmailContent($booking, $minutesBefore));

        // Envoyer l'email
        $this->mailer->send($email);

        // Log de la notification
        error_log(sprintf(
            'Rappel envoyé pour la réservation %d (client: %s, salle: %s) dans %d minutes',
            $booking->getId(),
            $client->getName(),
            $room->getName(),
            $minutesBefore
        ));
    }

    /**
     * Génère le contenu de l'email de rappel
     * 
     * @param Booking $booking La réservation
     * @param int $minutesBefore Minutes avant le début
     * @return string Contenu HTML de l'email
     */
    private function generateReminderEmailContent(Booking $booking, int $minutesBefore): string
    {
        $client = $booking->getClient();
        $room = $booking->getRoom();
        $startDate = $booking->getStartDate();
        $endDate = $booking->getEndDate();

        // Adapter le message selon le délai
        if ($minutesBefore >= 1440) { // Plus de 24h (1 jour)
            $timeMessage = "dans " . round($minutesBefore / 1440) . " jour(s)";
            $urgencyMessage = "Rappel avancé";
        } elseif ($minutesBefore >= 60) { // Plus d'1h
            $timeMessage = "dans " . round($minutesBefore / 60) . " heure(s)";
            $urgencyMessage = "Rappel";
        } else { // Moins d'1h
            $timeMessage = "dans {$minutesBefore} minute(s)";
            $urgencyMessage = "Rappel urgent";
        }

        return "
        <html>
        <body>
            <h2>{$urgencyMessage} de réservation</h2>
            <p>Bonjour {$client->getName()},</p>
            <p>Ceci est un rappel pour votre réservation qui commence {$timeMessage}.</p>
            
            <h3>Détails de la réservation :</h3>
            <ul>
                <li><strong>Salle :</strong> {$room->getName()}</li>
                <li><strong>Date de début :</strong> {$startDate->format('d/m/Y H:i')}</li>
                <li><strong>Date de fin :</strong> {$endDate->format('d/m/Y H:i')}</li>
                <li><strong>Capacité :</strong> {$room->getCapacity()} personnes</li>
            </ul>
            
            <p>Merci de votre confiance !</p>
        </body>
        </html>
        ";
    }

    /**
     * Récupère les réservations nécessitant une attention administrateur
     * 
     * @return array Réservations en attente de validation
     */
    public function getPendingBookings(): array
    {
        return $this->bookingRepository->findBy(['status' => 'pending']);
    }

    /**
     * Récupère les réservations en attente depuis plus de 5 jours
     * 
     * @return array Réservations nécessitant une validation urgente
     */
    public function getUrgentPendingBookings(): array
    {
        $fiveDaysAgo = new \DateTimeImmutable('-5 days');

        return $this->bookingRepository->createQueryBuilder('b')
            ->where('b.status = :status')
            ->andWhere('b.created_at <= :fiveDaysAgo')
            ->setParameter('status', 'pending')
            ->setParameter('fiveDaysAgo', $fiveDaysAgo)
            ->orderBy('b.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les réservations à venir dans les 5 prochains jours
     * 
     * @return array Réservations à venir
     */
    public function getUpcomingBookings(): array
    {
        $now = new \DateTimeImmutable();
        $fiveDaysFromNow = new \DateTimeImmutable('+5 days');

        return $this->bookingRepository->createQueryBuilder('b')
            ->where('b.startDate >= :now')
            ->andWhere('b.startDate <= :fiveDaysFromNow')
            ->setParameter('now', $now)
            ->setParameter('fiveDaysFromNow', $fiveDaysFromNow)
            ->orderBy('b.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les statistiques pour le dashboard
     * 
     * @return array Statistiques des réservations
     */
    public function getDashboardStats(): array
    {
        $totalBookings = $this->bookingRepository->count([]);
        $pendingBookings = $this->bookingRepository->count(['status' => 'pending']);
        $confirmedBookings = $this->bookingRepository->count(['status' => 'confirmed']);
        $cancelledBookings = $this->bookingRepository->count(['status' => 'cancelled']);
        $urgentBookings = count($this->getUrgentPendingBookings());

        return [
            'total' => $totalBookings,
            'pending' => $pendingBookings,
            'confirmed' => $confirmedBookings,
            'cancelled' => $cancelledBookings,
            'urgent' => $urgentBookings,
        ];
    }

    /**
     * Vérifie et retourne les notifications urgentes
     * 
     * @return array Notifications à afficher
     */
    public function checkAndGetUrgentNotifications(): array
    {
        $notifications = [];

        $urgentBookings = $this->getUrgentPendingBookings();

        if (!empty($urgentBookings)) {
            $count = count($urgentBookings);
            $notifications[] = [
                'type' => 'danger',
                'message' => "⚠️ ATTENTION : {$count} réservation(s) en attente depuis plus de 5 jours nécessitent une validation urgente !"
            ];
        }

        $upcomingBookings = $this->getUpcomingBookings();
        $pendingUpcoming = array_filter($upcomingBookings, fn($booking) => $booking->getStatus() === 'pending');

        if (!empty($pendingUpcoming)) {
            $count = count($pendingUpcoming);
            $notifications[] = [
                'type' => 'warning',
                'message' => "📅 {$count} réservation(s) à venir dans les 5 prochains jours nécessitent une validation."
            ];
        }

        return $notifications;
    }
}