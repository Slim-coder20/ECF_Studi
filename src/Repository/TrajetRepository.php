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

    /**
     * Recherche des trajets selon les critères du formulaire.
     */
    public function findBySearch(TrajetSearch $search): array
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.nbPlaces > 0');

        $joinedChauffeur = false;

        if ($search->noteMin) {
            $qb->leftJoin('t.chauffeur', 'u')
               ->addSelect('u')
               ->andWhere('u.note >= :note')
               ->setParameter('note', $search->noteMin);
            $joinedChauffeur = true;
        }

        if ($search->villeDepart) {
            $qb->andWhere('t.villeDepart LIKE :vd')
               ->setParameter('vd', '%' . $search->villeDepart . '%');
        }

        if ($search->villeArrivee) {
            $qb->andWhere('t.villeArrivee LIKE :va')
               ->setParameter('va', '%' . $search->villeArrivee . '%');
        }

       if ($search->date) {
        $startDate = (clone $search->date)->setTime(0, 0, 0);
        $endDate = (clone $search->date)->setTime(23, 59, 59);

        $qb->andWhere('t.dateDepart BETWEEN :start AND :end')
        ->setParameter('start', $startDate)
        ->setParameter('end', $endDate);
       }


        if ($search->prixMax) {
            $qb->andWhere('t.prix <= :prixMax')
               ->setParameter('prixMax', $search->prixMax);
        }

        if ($search->dureeMax) {
            $qb->andWhere('t.dateDepart >= :now')
            ->andWhere('t.dateDeprt <= :dureeMax')
            ->setParameter('now', new \DateTime())
            ->setParameter('nextWeek', (new \DateTime())->modify('+7 days'));
        }

        return $qb->getQuery()->getResult();
    }
}
