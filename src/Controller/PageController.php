<?php

namespace App\Controller;


use App\Repository\RoomRepository;
use App\Repository\LocationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PageController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('page/home.html.twig', [
        
        ]);
    }

    #[Route('/region/{region}', name: 'location', methods: ['GET'])]
    public function location(string $region, RoomRepository $rr, LocationRepository $lr): Response
    {
        $location = $lr->findOneByState($region);
        return $this->render('page/region.html.twig', [
        'rooms' => $rr->serachByRegion($location->getId()),
        'region' => 'TEST',
        ]);
    }
}
