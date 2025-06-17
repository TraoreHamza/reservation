<?php


namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewForm;
use App\Repository\RoomRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/review')]
class ReviewController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ReviewRepository $rr,
        private RoomRepository $roomRepo

    ) {}

    #[Route('/new/{id}', name: 'review_new', methods: ['POST'])]
    public function new(Request $request, int $id): Response
    {
        $review = new Review();
        $data = $request->request->all();
        $review
            ->setRoom($this->roomRepo->find($id))
            ->setStar($data['star'])
            ->setContent($data['content'])
        ;


        $this->em->persist($review);
        $this->em->flush();
        $this->addFlash('success', 'Avis ajouté');
        return $this->redirectToRoute('room_index');


        return $this->render('review/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'review_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Review $review): Response
    {
        $form = $this->createForm(ReviewForm::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Avis mis à jour');
            return $this->redirectToRoute('room_view', ['id' => $review->getRoom()->getId()]);
        }

        return $this->render('review/edit.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/delete', name: 'review_delete', methods: ['POST'])]
    public function delete(Review $review): Response
    {
        $this->em->remove($review);
        $this->em->flush();
        $this->addFlash('success', 'Avis supprimé');
        return $this->redirectToRoute('room_view', ['id' => $review->getRoom()->getId()]);
    }
}
