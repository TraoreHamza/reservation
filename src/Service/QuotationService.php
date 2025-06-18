<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Quotation;
use Doctrine\ORM\EntityManagerInterface;

class QuotationService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function createFromBooking(Booking $booking): Quotation
    {
        $quotation = new Quotation();
        $quotation->setClient($booking->getUser()->getClient());
        $quotation->setRoom($booking->getRoom());
        $quotation->setBooking($booking);
        $quotation->setPrice(100); // prix à calculer dynamiquement plus tard
        $quotation->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($quotation);
        $this->em->flush();

        return $quotation;
    }
}
