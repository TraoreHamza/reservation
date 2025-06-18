<?php

namespace App\EventListener;

use App\Repository\ClientRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class VerificationListener
{
    public function __construct(
        private ClientRepository $clientRepo,
        private Security $security,
        private SessionInterface $session
    ) {}

    public function onKernelController(ControllerEvent $event): void
    {
        $user = $this->security->getUser();

        if (!$user) {
            return; // aucun utilisateur connecté
        }

        $client = $this->clientRepo->findOneBy(['user' => $user]);

        if (!$client || !$client->getAdresse()) {
            $this->session->getFlashBag()->add(
                'warning',
                'Veuillez compléter votre fiche client avant de continuer.'
            );
        }
    }
}
