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
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{


    public function __construct(
        private BookingRepository $br 
    ){}
    
    public function index(): Response
    {
    return $this->render('admin/dashboard.html.twig', [
        'bookings' => $this->br->findAll(),
    ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Reservation');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Room', 'fas fa-list', Room::class);
        yield MenuItem::linkToCrud('Option', 'fas fa-list', Option::class);
        yield MenuItem::linkToCrud('Equipment', 'fas fa-list', Equipment::class);
        yield MenuItem::linkToCrud('Client', 'fas fa-list', Client::class);
        yield MenuItem::linkToCrud('Booking', 'fas fa-list', Booking::class);
        yield MenuItem::linkToCrud('Quotation', 'fas fa-list', Quotation::class);
    }
}
