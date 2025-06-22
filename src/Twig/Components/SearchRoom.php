<?php

namespace App\Twig\Components;

use App\Repository\RoomRepository;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent("SearchRoom", template: "components/SearchRoom.html.twig")]
final class SearchRoom
{
    use DefaultActionTrait;
    /**
     * LiveProp est une classe qu'on utilise en annotation pour définir les propriétés "Live"
     * que l'on va utiliser dans le composant. C'est comme le passage de props en JavaScript.
     * 
     * "writable : true" signifie que la propriété est modifiable depuis le composant.
     * "url : true" signifie que la propriété sera disponible dans l'URL.
     */

    #[LiveProp(writable: true, url: true)]
    public ?string $query = null;

    #[LiveProp(writable: true, url: true)]
    public ?string $equipment = null;

    #[LiveProp(writable: true, url: true)]
    public ?string $option = null;


    #[LiveProp(writable: true, url: true)]
    public ?string $location = null;

    public function __construct(private RoomRepository $rr) {}



    public function getRooms(): array

    {
        if ($this->query || $this->equipment || $this->option || $this->location) { //s'il y a une requete query les articles correspondants
            return $this->rr->searchRooms(
                $this->query ?? null,
                $this->option ?? null,
                $this->equipment ?? null,
                $this->location ?? null
            );
        }

        return $this->rr->findBy([], ['name' => 'ASC'], 4) ?? []; //sinon les 10 derniers articles par defaut
    }
}
