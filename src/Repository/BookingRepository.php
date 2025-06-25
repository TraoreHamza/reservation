<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @return Booking[] Returns an array of validated bookings starting in the next 7 days
     */
    public function findUpcomingValidatedBookings(): array
    {
        $now = new \DateTime();
        $sevenDaysFromNow = (clone $now)->modify('+7 days');

        return $this->createQueryBuilder('b')
            ->andWhere('b.status = :status')
            ->andWhere('b.startDate >= :now')
            ->andWhere('b.startDate <= :sevenDaysFromNow')
            ->setParameter('status', 'validated')
            ->setParameter('now', $now)
            ->setParameter('sevenDaysFromNow', $sevenDaysFromNow)
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
