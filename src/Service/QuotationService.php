<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Quotation;
use Doctrine\ORM\EntityManagerInterface;

class QuotationService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function create(Booking $booking): Quotation
    {
        $quotation = new Quotation();
        

        // Association de la réservation à la quotation
        $quotation->setBooking($booking);

        // Calcul du prix en fonction du nombre de jours

        $startDate = $booking->getStartDate();
        $endDate = $booking->getEndDate();
        $dailyRate = $booking->getRoom()->getDailyRate();


        if ($startDate && $endDate && $dailyRate > 0) {
            $days = $startDate->diff($endDate)->days; // Calcul du nombre de jours
            if ($days < 1) {
                $days = 1; // Assurer qu'il y a au moins un jour de réservation
            }
            $price = $days * $dailyRate;


            $quotation->setPrice((string)$price); // Conversion en string pour correspondre au type défini dans Quotation
        } else {
            $quotation->setPrice('100'); // Valeur par défaut si les données sont manquantes
        }

        $quotation->setDate('Du ' . $startDate->format('d-m-Y') . ' au ' . $endDate->format('d-m-Y'));

        // Persister et sauvegarder la quotation
        $this->em->persist($quotation);
        $this->em->flush();

        return $quotation;
    }
}