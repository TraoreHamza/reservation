<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use App\Entity\Client;
use App\Entity\Option;
use App\Entity\Booking;
use App\Entity\Equipment;
use App\Entity\Quotation;
use App\Controller\RoomController;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Contracts\Cache\CacheInterface;


#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{


    public function __construct(
        private BookingRepository $br
    ) {}

    public function index(): Response
    {
        $bookings = $this->br->findAll();
        $datas = [];                                    // / tableau de données pour le calendrier au forma json
        foreach ($bookings as $booking) {

            $datas[] = [
                'title' => $booking->getRoom()->getName() . " " . $booking->getStatus() . " " . $booking->getClient()->getName(),
                'start' => $booking->getStartDate()->format('Y-m-d H:i:s'),
                'end' => $booking->getEndDate()->format('Y-m-d H:i:s'),
                "color" => ($booking->getStatus() === 'pending' ? '#f87171' : "") .
                    ($booking->getStatus() === 'confirmed' ? '#10b981' : "") .
                    ($booking->getStatus() === 'cancelled' ? '#a8a29e' : ""),

            ];
        }
        $bookings = json_encode($datas);
        //dd($bookings);

        return $this->render('admin/dashboard.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('sallevenue');
    }

    public function configureAssets(): Assets                                     // integration calendar fullcalendar ou n importe quelle autre librairie JS
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
        yield MenuItem::linkToRoute('Back to site', 'fa-solid fa-arrow-left', 'home');
    }

}