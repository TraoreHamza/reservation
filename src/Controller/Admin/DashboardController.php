<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use App\Entity\Client;
use App\Entity\Option;
use App\Entity\Booking;
use App\Entity\Equipment;
use App\Entity\Quotation;
use App\Service\NotificationService;
use App\Controller\RoomController;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private BookingRepository $br,
        private NotificationService $notificationService
    ) {}

    public function index(): Response
    {
        // Récupération des notifications urgentes
        $urgentNotifications = $this->notificationService->checkAndGetUrgentNotifications();

        // Ajout des notifications flash
        foreach ($urgentNotifications as $notification) {
            $this->addFlash($notification['type'], $notification['message']);
        }

        // Récupération des statistiques
        $stats = $this->notificationService->getDashboardStats();

        $bookings = $this->br->findAll();
        $datas = [];                                    // tableau de données pour le calendrier au format json
        foreach ($bookings as $booking) {
            // Code couleur selon le statut
            $color = match ($booking->getStatus()) {
                'pending' => '#f87171',    // Rouge pour en attente
                'confirmed' => '#10b981',  // Vert pour confirmée
                'cancelled' => '#a8a29e',  // Gris pour annulée
                default => '#6b7280'       // Gris par défaut
            };

            $datas[] = [
                'title' => $booking->getRoom()->getName() . " - " . $booking->getStatus() . " - " . ($booking->getClient()?->getName() ?? 'Client inconnu'),
                'start' => $booking->getStartDate()->format('Y-m-d H:i:s'),
                'end' => $booking->getEndDate()->format('Y-m-d H:i:s'),
                'color' => $color,
                'extendedProps' => [
                    'status' => $booking->getStatus(),
                    'room' => $booking->getRoom()->getName(),
                    'client' => $booking->getClient()?->getName() ?? 'Client inconnu'
                ]
            ];
        }
        $bookings = json_encode($datas);

        return $this->render('admin/dashboard.html.twig', [
            'bookings' => $bookings,
            'stats' => $stats,
            'urgentNotifications' => $urgentNotifications
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SalleVenue - Administration');
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addJsFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Room', 'fa-solid fa-people-roof', Room::class);
        yield MenuItem::linkToCrud('Option', 'fa-solid fa-filter', Option::class);
        yield MenuItem::linkToCrud('Equipment', 'fa fa-cog', Equipment::class);
        yield MenuItem::linkToCrud('Client', 'fa fa-users', Client::class);
        yield MenuItem::linkToCrud('Booking', 'fas fa-list', Booking::class);
        yield MenuItem::linkToCrud('Quotation', 'fa-solid fa-euro-sign', Quotation::class);
        yield MenuItem::linkToRoute('Documentation', 'fa-solid fa-book', 'admin_documentation');
        yield MenuItem::linkToRoute('Back to site', 'fa-solid fa-arrow-left', 'home');
    }
}
