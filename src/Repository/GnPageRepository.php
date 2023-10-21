<?php

namespace App\Repository;

use App\Entity\GnPage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GnPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method GnPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method GnPage[]    findAll()
 * @method GnPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GnPageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GnPage::class);
    }

    // /**
    //  * @return GnPage[] Returns an array of GnPage objects
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
    public function findOneBySomeField($value): ?GnPage
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
