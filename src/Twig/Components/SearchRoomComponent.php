<?php

namespace App\Twig\Components;

use App\Repository\RoomRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('search_room_component')]
#[AsLiveComponent]
class SearchRoomComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(private RoomRepository $roomRepository)
    {
        // Plus besoin d'initialiser $rooms ici
    }

    public function getRooms(): array
    {
        if (strlen($this->query) < 2) {
            // Affiche toutes les salles si la recherche est vide ou trop courte
            return $this->roomRepository->findAll();
        }
        return $this->roomRepository->searchByName($this->query);
    }
}
