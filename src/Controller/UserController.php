<?php


namespace App\Controller;

use App\Entity\User;
use App\Entity\Client;
use App\Form\UserForm;
use App\Entity\Favorite;
use App\Form\ClientForm;
use App\Form\ProfileForm;
use App\Repository\RoomRepository;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private FavoriteRepository $fr,
        private RoomRepository $rr
    ) {}

    #[Route('/profile', name: 'user_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Profil mis à jour');
        }

        $favorites = $this->fr->findBy(['user' => $user]);

        return $this->render('user/profile.html.twig', [
            'profileForm' => $form,
            'favorites' => $favorites
        ]);
    }

    #[Route('/fiche', name: 'user_fiche', methods: ['GET', 'POST'])]
    public function fiche(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $client = $user->getClient() ?? new Client();
        $form = $this->createForm(ClientForm::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setUser($user);
            $this->em->persist($client);
            $this->em->flush();
            $this->addFlash('success', 'Fiche client mise à jour');
        }

        return $this->render('user/fiche.html.twig', [
            'clientForm' => $form
        ]);
    }

    #[Route('/favorite/{roomId}', name: 'user_favorite_toggle', methods: ['POST'])]
    public function favorite(int $roomId): Response
    {
        $user = $this->getUser();
        $favorite = $this->fr->findOneBy(['user' => $user, 'room' => $roomId]);

        if ($favorite) {
            $this->em->remove($favorite);
        } else {
            $newFav = new Favorite();
            $newFav->setUser($user);
            $newFav->setRoom($this->rr->find($roomId));
            $this->em->persist($newFav);
        }

        $this->em->flush();
        return $this->redirectToRoute('room_view', ['id' => $roomId]);
    }
}
