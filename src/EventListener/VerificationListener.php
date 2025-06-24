<?php

namespace App\EventListener;

use App\Entity\User;
use App\Repository\ClientRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class VerificationListener
{
    public function __construct(
        private ClientRepository $clientRepo,
        private Security $security,
        private RequestStack $requestStack
    ) {}

    public function onKernelController(ControllerEvent $event): void
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        if (!$user) {
            return; // aucun utilisateur connecté
        }

        $client = $user->getClient();

        if (!$client || !$client->getAddress()) {
            /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
            $session = $this->requestStack->getSession();
            if ($session) {
                $session->getFlashBag()->add(
                    'warning',
                    'Veuillez compléter votre fiche client avant de continuer.'
                );
            }
        }
    }
}
