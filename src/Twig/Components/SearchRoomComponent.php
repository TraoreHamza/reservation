<?php

namespace App\Twig\Components;

use App\Repository\RoomRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('search_room_component')]
#[AsLiveComponent('SearchRoomComponent', template: 'components/search_room_component.html.twig')]
class SearchRoomComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true, url: true)]
    public ?string $query = null;

    public function __construct(private RoomRepository $roomRepository) {}

    public function getRooms(): array
    {
        if ($this->query) {
            return $this->roomRepository->searchRooms($this->query);
        }
        return $this->roomRepository->findBy([], ['id' => 'DESC'], 10);
    }

    // public function getResults(): array
    // {
    //     return $this->rooms;
    // }
}
