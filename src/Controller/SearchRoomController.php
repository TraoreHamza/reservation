<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchRoomController extends AbstractController
{
    #[Route('/search-room', name: 'search_room', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('search_room/index.html.twig');
    }
}
