<?php

namespace App\Repository;

use App\Entity\GnUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GnUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method GnUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method GnUser[]    findAll()
 * @method GnUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GnUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GnUser::class);
    }
    
    /**
     * Vérifier users confirmAt not null
     * @param int $id
     * @return bool
     */
    public function getOneUserNotAbonne($id)
    {
        $bool = false;
        $user = GnUser::class;
        $users = $this->getEntityManager()->createQuery(
            "SELECT u
            FROM $user u
            LEFT JOIN u.gnRoles r
            WHERE u.id = :user_id AND u.confirmAt IS NOT NULL AND u.gnSubscription IS NOT NULL"
        );
        $users->setParameter('user_id', $id);
        

        
        if($users->getResult()){
            $bool = true;
        }
        return $bool;
  
        
    }

    /**
     * Vérifier users confirmAt not null
     * @param string $identifiant
     * @return array
     */
    public function findByLoginOrEmail($identifiant)
    {
        $user = GnUser::class;
        $query = $this->getEntityManager()->createQuery(
            "SELECT u
            FROM $user u
            LEFT JOIN u.gnRoles r
            WHERE (u.userEmail = :identifiant  OR u.userName = :identifiant)"
        );
        $query->setParameter('identifiant', $identifiant);

        return (is_array($query->getResult()) && !empty($query->getResult())) ? $query->getResult()[0] : [];
    }

    /**
     * Vérifier users confirmAt not null
     * @param int $id
     * @return bool
     */
    public function getOneAdmin($id)
    {
        $bool = false;
        $user = GnUser::class;
        $users = $this->getEntityManager()->createQuery(
            "SELECT u
            FROM $user u
            LEFT JOIN u.gnRoles r
            WHERE u.id = :user_id AND r.id = :role_id"
        );
        $users->setParameter('user_id', $id);
        $users->setParameter('role_id', 2);

        if($users->getResult()){
            $bool = true;
        }
        return $bool;
  
        
    }

    /**
     * Vérifier users confirmAt not null
     * @return user
     */
    public function getJournalist()
    {

        $user = GnUser::class;
        $users = $this->getEntityManager()->createQuery(
            "SELECT u
            FROM $user u
            LEFT JOIN u.gnRoles r
            WHERE r.id = :role_id"
        );
        $users->setParameter('role_id', 3);

        return  $users->getResult();

    }

    
    // /**
    //  * @return GnUser[] Returns an array of GnUser objects
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
    public function findOneBySomeField($value): ?GnUser
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
