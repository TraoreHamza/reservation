<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Room
 * 
 * AMÉLIORATIONS APPORTÉES (Lawrence + Assistant) :
 * - Recherche multi-critères complète
 * - Filtrage par tous les attributs de Room et ses relations
 * - Ajout des champs address dans Location et Client
 * - Optimisation des requêtes avec LEFT JOIN
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
     * Recherche simple par nom et critères de base
     * Utilisée sur la page d'accueil pour la recherche dynamique
     * 
     * CRITÈRES DE RECHERCHE :
     * - Nom de la chambre (insensible à la casse)
     * - Description de la chambre
     * - Ville de la localisation
     * - Nom des équipements
     * - Nom des options
     * 
     * FILTRES APPLIQUÉS :
     * - Seulement les chambres disponibles (isAvailable = true)
     * - Tri par nom de chambre (ASC)
     * 
     * @param string $query Terme de recherche
     * @return Room[] Liste des chambres correspondantes
     */
    public function searchByName(string $query): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.location', 'l')
            ->leftJoin('r.equipments', 'e')
            ->leftJoin('r.options', 'o')
            ->where('LOWER(r.name) LIKE LOWER(:q)')
            ->orWhere('LOWER(r.description) LIKE LOWER(:q)')
            ->orWhere('LOWER(l.city) LIKE LOWER(:q)')
            ->orWhere('LOWER(e.name) LIKE LOWER(:q)')
            ->orWhere('LOWER(o.name) LIKE LOWER(:q)')
            ->andWhere('r.isAvailable = true')
            ->setParameter('q', '%' . strtolower($query) . '%')
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * RECHERCHE AVANCÉE - Méthode principale pour la page de recherche dédiée
     * 
     * AMÉLIORATIONS MAJEURES (Lawrence + Assistant) :
     * - Ajout de TOUS les attributs de Room et ses relations
     * - Recherche dans les adresses (Location et Client)
     * - Recherche par capacité, département, état, numéro
     * - Recherche par type d'équipement
     * 
     * CRITÈRES DE RECHERCHE COMPLETS :
     * 
     * CHAMBRE (Room) :
     * - r.name → Nom de la chambre
     * - r.description → Description
     * - r.capacity → Capacité
     * 
     * LOCALISATION (Location) :
     * - l.city → Ville
     * - l.department → Département
     * - l.state → État/Région
     * - l.number → Numéro
     * - l.address → Adresse (AJOUTÉ)
     * 
     * ÉQUIPEMENTS (Equipment) :
     * - e.name → Nom de l'équipement
     * - e.type → Type d'équipement (AJOUTÉ)
     * 
     * OPTIONS (Option) :
     * - o.name → Nom de l'option
     * 
     * CLIENTS (Client) :
     * - c.address → Adresse du client (AJOUTÉ)
     * 
     * FILTRES APPLIQUÉS :
     * - Seulement les chambres disponibles
     * - Tri par nom de chambre
     * - Limite de 10 résultats pour les performances
     * 
     * @param string $query Terme de recherche
     * @return array Liste des chambres correspondantes
     */
    public function searchRooms(string $query): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.location', 'l')
            ->leftJoin('r.equipments', 'e')
            ->leftJoin('r.options', 'o')
            ->leftJoin('r.bookings', 'b')
            ->leftJoin('b.client', 'c')
            ->where('r.name LIKE :q')
            ->orWhere('r.description LIKE :q')
            ->orWhere('r.capacity LIKE :q')
            ->orWhere('l.city LIKE :q')
            ->orWhere('l.department LIKE :q')
            ->orWhere('l.state LIKE :q')
            ->orWhere('l.number LIKE :q')
            ->orWhere('l.address LIKE :q')
            ->orWhere('e.name LIKE :q')
            ->orWhere('e.type LIKE :q')
            ->orWhere('o.name LIKE :q')
            ->orWhere('c.address LIKE :q')
            ->andWhere('r.isAvailable = true')
            ->setParameter('q', '%' . $query . '%')
            ->orderBy('r.name', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


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
