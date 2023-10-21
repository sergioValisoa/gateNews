<?php

namespace App\Repository;

use App\Entity\GnCountry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GnCountry|null find($id, $lockMode = null, $lockVersion = null)
 * @method GnCountry|null findOneBy(array $criteria, array $orderBy = null)
 * @method GnCountry[]    findAll()
 * @method GnCountry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GnCountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GnCountry::class);
    }

    // /**
    //  * @return GnCountry[] Returns an array of GnCountry objects
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
    public function findOneBySomeField($value): ?GnCountry
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
