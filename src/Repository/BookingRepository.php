<?php

namespace App\Repository;

use DateTimeInterface;
use App\Entity\Booking;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }




    public function findBookingsNotProcessedBefore(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.processed = false')
            ->andWhere('b.startDate <= :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return Booking[] Returns an array of Booking objects
     */
    public function findUnprocessedBookingsStartingBefore(DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.startDate <= :date')
            ->andWhere('b.status = :status') // Assurez-vous que 'status' existe et a une valeur 'pending'
            ->setParameter('date', $date)
            ->setParameter('status', 'pending') // Remplacez 'pending' par le statut de vos réservations non traitées
            ->orderBy('b.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Booking[] Returns an array of Booking objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Booking
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
