<?php

namespace App\Service;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Service de gestion des notifications administrateur
 * 
 * FONCTIONNALITÉS :
 * - Alerte 5 jours avant réservation non validée
 * - Notifications sur le dashboard
 * - Gestion des statuts de réservation
 */
class NotificationService
{
    public function __construct(
        private BookingRepository $bookingRepository,
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack
    ) {}

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
