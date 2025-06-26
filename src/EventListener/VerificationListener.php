<?php

namespace App\EventListener;

use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class VerificationListener
{
    public function __construct(
        private Security $security,
        private RequestStack $requestStack
    ) {}

    public function onKernelController(ControllerEvent $event): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if (!$user) {
            return; // aucun utilisateur connecté
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true) && !$user->getClient()) {
            $session = $this->requestStack->getSession();

            if ($session instanceof SessionInterface) {
                $session->getFlashBag()->add(
                    'warning',
                    'Veuillez compléter votre fiche client avant de continuer.'
                );
            }
        }
    }
}
