<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('page/home.html.twig', [
        
        ]);
    }

    #[Route('/region/{region}', name: 'location', methods: ['GET'])]
    public function location(string $region, LocationRepository $rr): Response
    {
        return $this->render('page/region.html.twig', [
        'rooms' => $rr->findOneBy(['state' => $region])->getRooms(),
        'region' => $region,
        ]);
    }
}
