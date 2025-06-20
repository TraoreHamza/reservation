<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    //    /**
    //     * @return Room[] Returns an array of Room objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Room
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Recherche les salles par nom (contient la chaîne $query)
     * @param string $query
     * @return Room[]
     */
    public function searchByName(string $query): array
    {
        return $this->createQueryBuilder('r')
            ->where('LOWER(r.name) LIKE LOWER(:q)')
            ->setParameter('q', '%' . strtolower($query) . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche les salles par différents critères
     * @param string $query
     * @return array
     */
    public function searchRooms(string $query): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.options', 'o')
            ->leftJoin('r.equipments', 'e')
            ->where('r.name LIKE :q')
            ->orWhere('r.description LIKE :q')
            ->orWhere('o.name LIKE :q')
            ->orWhere('e.name LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}

// namespace App\Repository;

// use App\Entity\Room;
// use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
// use Doctrine\Persistence\ManagerRegistry;

// /**
//  * @extends ServiceEntityRepository<Room>
//  */
// class RoomRepository extends ServiceEntityRepository
// {
//     public function __construct(ManagerRegistry $registry)
//     {
//         parent::__construct($registry, Room::class);
//     }

//     /**
//      * Recherche les salles par différents critères
//      * @param string $query
//      * @return array
//      */
//     public function searchRooms(string $query): array
//     {
//         $qb = $this->createQueryBuilder('r')
//             ->leftJoin('r.options', 'o')
//             ->leftJoin('r.category', 'c')
//             ->leftJoin('r.location', 'l')
//             ->where('r.name LIKE :q')
//             ->orWhere('o.name LIKE :q')
//             ->orWhere('c.name LIKE :q')
//             ->orWhere('l.city LIKE :q')
//             ->setParameter('q', '%'.$query.'%')
//             ->setMaxResults(10);

//         $results = $qb->getQuery()->getResult();

//         return array_map(function ($room) {
//             return [
//                 'id' => $room->getId(),
//                 'name' => $room->getName(),
//                 'location' => $room->getLocation()?->getCity() ?? '',
//                 'category' => $room->getCategory()?->getName() ?? '',
//             ];
//         }, $results);
//     }
// }
