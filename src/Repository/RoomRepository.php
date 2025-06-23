<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Room
 * 
 * AMÉLIORATIONS APPORTÉES (Lawrence + Yasmina + Assistant) :
 * - Fusion des méthodes de recherche de Lawrence et Yasmina
 * - Recherche multi-critères complète et flexible
 * - Filtrage par tous les attributs de Room et ses relations
 * - Optimisation des requêtes avec LEFT JOIN et distinct
 * 
 * @extends ServiceEntityRepository<Room>
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    /**
     * Recherche avancée et flexible des chambres
     * 
     * Cette méthode combine les approches de Lawrence et Yasmina pour offrir une recherche puissante.
     * Elle est utilisée à la fois pour la recherche dynamique et la page de recherche dédiée.
     * 
     * CRITÈRES DE RECHERCHE :
     * - `query`: Terme de recherche principal (nom, description, ville...)
     * - `option`: Nom d'une option spécifique
     * - `equipment`: Nom d'un équipement spécifique
     * - `location`: Département ou ville
     * 
     * @param string|null $query Terme de recherche général
     * @param string|null $option Filtre par nom d'option
     * @param string|null $equipment Filtre par nom d'équipement
     * @param string|null $location Filtre par localisation (département/ville)
     * @return Room[] Liste des chambres correspondantes
     */
    public function searchRooms(?string $query, ?string $option, ?string $equipment, ?string $location): array
    {
        $qb = $this
            ->createQueryBuilder('r')
            ->leftJoin('r.location', 'l')
            ->leftJoin('r.equipments', 'e')
            ->leftJoin('r.options', 'o')
            ->where('r.isAvailable = true');

        if ($query) {
            $qb->andWhere('LOWER(r.name) LIKE LOWER(:query) OR LOWER(r.description) LIKE LOWER(:query) OR LOWER(l.city) LIKE LOWER(:query)')
               ->setParameter('query', '%' . $query . '%');
        }

        if ($option) {
            $qb->andWhere('LOWER(o.name) LIKE LOWER(:option)')
               ->setParameter('option', '%' . $option . '%');
        }

        if ($equipment) {
            $qb->andWhere('LOWER(e.name) LIKE LOWER(:equipment)')
               ->setParameter('equipment', '%' . $equipment . '%');
        }

        if ($location) {
            $qb->andWhere('LOWER(l.department) LIKE LOWER(:location) OR LOWER(l.city) LIKE LOWER(:location)')
               ->setParameter('location', '%' . $location . '%');
        }

        return $qb->orderBy('r.name', 'ASC')
                  ->distinct()
                  ->getQuery()
                  ->getResult();
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
