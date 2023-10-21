<?php

namespace App\Repository;

use App\Entity\GnSubscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GnSubscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method GnSubscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method GnSubscription[]    findAll()
 * @method GnSubscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GnSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GnSubscription::class);
    }

    // /**
    //  * @return GnSubscription[] Returns an array of GnSubscription objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GnSubscription
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
