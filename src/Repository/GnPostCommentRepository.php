<?php

namespace App\Repository;

use App\Entity\GnPostComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GnPostComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method GnPostComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method GnPostComment[]    findAll()
 * @method GnPostComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GnPostCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GnPostComment::class);
    }

    // /**
    //  * @return GnPostComment[] Returns an array of GnPostComment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GnPostComment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
