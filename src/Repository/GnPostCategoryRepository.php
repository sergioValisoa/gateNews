<?php

namespace App\Repository;

use App\Entity\GnPostCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GnPostCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method GnPostCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method GnPostCategory[]    findAll()
 * @method GnPostCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GnPostCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GnPostCategory::class);
    }

    // /**
    //  * @return GnPostCategory[] Returns an array of GnPostCategory objects
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
    public function findOneBySomeField($value): ?GnPostCategory
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
