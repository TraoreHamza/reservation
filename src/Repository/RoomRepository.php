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


    /** 
     * @return Room[]  // Returns an array of room objects
     */
    public function searchRooms(?string $query, ?string $option, ?string $equipment, ?string $location): array
    {
        $qb = $this
            ->createQueryBuilder('r') //définition du query builder
            ->leftJoin('r.options', 'o') // on fait une jointure avec la table des options
            ->leftJoin('r.equipments', 'e') // on fait une jointure avec la table des équipements
            ->leftJoin('r.location', 'l'); // on fait une jointure avec la table des locations

        if ($query) {
            $qb->andWhere('r.name LIKE :val OR r.description LIKE :val') // on cherche le titre ou la description
                ->setParameter('val', '%' . strtolower($query) . '%'); // on met en minuscule et on ajoute les % pour la recherche
        }

        if ($option) {
            $qb->andWhere('o.name LIKE :option') // on cherche l'option
                ->setParameter('option', '%' . strtolower($option) . '%'); // on met en minuscule et on ajoute les % pour la recherche 
        }

        if ($equipment) {
            $qb->andWhere('e.name LIKE :equipment') // on cherche l'équipement
                ->setParameter('equipment', '%' . strtolower($equipment) . '%'); // on met en minuscule et on ajoute les % pour la recherche
        }

        if ($location) {
            $qb->andWhere('l.department LIKE :location') // on cherche la localisation
                ->setParameter('location', '%' . strtolower($location) . '%'); // on met en minuscule et on ajoute les % pour la recherche
        }

        $qb->distinct() // on utilise distinct pour ne pas avoir de doublons
            ->orderBy('r.name', 'ASC'); // on trie par nom de la salle
        return $qb->getQuery()->getResult(); // on retourne le résultat de la requête


        ;
    }


    /**
     * Retourne le tableau de salle en fonction de la région sélectionnée
     * @return Room[] 
     */
    public function serachByRegion(string $region)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.location = :region')
            ->setParameter('region', $region)
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
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
