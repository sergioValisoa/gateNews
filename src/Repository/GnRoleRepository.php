<?php

namespace App\Repository;

use App\Entity\GnRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GnRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method GnRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method GnRole[]    findAll()
 * @method GnRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GnRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GnRole::class);
    }

    // /**
    //  * @return GnRole[] Returns an array of GnRole objects
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
    public function findOneBySomeField($value): ?GnRole
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
