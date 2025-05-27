<?php

namespace App\Repository;

use App\Entity\Trajet;
use App\Model\TrajetSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trajet>
 */
class TrajetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trajet::class);
    }

//    /**
//     * @return Trajet[] Returns an array of Trajet objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Trajet
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    // Cette méthode permet de rechercher des trajets en fonction des critères de recherche fournis.


public function findBySearch(TrajetSearch $search): array
{
    $qb = $this->createQueryBuilder('t')
        ->andWhere('t.nbPlaces > 0');

    if ($search->villeDepart) {
        $qb->andWhere('t.villeDepart LIKE :vd')
           ->setParameter('vd', '%' . $search->villeDepart . '%');
    }

    if ($search->villeArrivee) {
        $qb->andWhere('t.villeArrivee LIKE :va')
           ->setParameter('va', '%' . $search->villeArrivee . '%');
    }

    if ($search->date) {
        $dateStart = (clone $search->date)->setTime(0, 0, 0);
        $dateEnd = (clone $search->date)->setTime(23, 59, 59);

        $qb->andWhere('t.dateDepart BETWEEN :start AND :end')
           ->setParameter('start', $dateStart)
           ->setParameter('end', $dateEnd);
    }

    return $qb->getQuery()->getResult();
}


}
