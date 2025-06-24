<?php

namespace App\EventListener;

use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class VerificationListener
{
    public function __construct(
        private Security $security,
        private RequestStack $requestStack
    ) {}

    public function onKernelController(ControllerEvent $event): void
    {
<<<<<<< HEAD
        /** @var User|null $user */
=======
        /** @var User $user */
>>>>>>> origin/hamza
        $user = $this->security->getUser();

        if (!$user) {
            return; // aucun utilisateur connecté
        }

        $client = $user->getClient();

<<<<<<< HEAD
        if (!$client || !$client->getAddress()) {
=======
        if (!$client) {
>>>>>>> origin/hamza
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