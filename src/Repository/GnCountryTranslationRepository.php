<?php

namespace App\Repository;

use App\Entity\GnCountryTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GnCountryTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GnCountryTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GnCountryTranslation[]    findAll()
 * @method GnCountryTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GnCountryTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GnCountryTranslation::class);
    }

    // /**
    //  * @return GnCountryTranslation[] Returns an array of GnCountryTranslation objects
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
    public function findOneBySomeField($value): ?GnCountryTranslation
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
