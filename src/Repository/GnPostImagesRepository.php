<?php

namespace App\Repository;

use App\Entity\GnPostImages;
use App\Entity\GnPostImagesCategory;
use App\Service\MetierManagerBundle\Utils\EntityName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method GnPostImages|null find($id, $lockMode = null, $lockVersion = null)
 * @method GnPostImages|null findOneBy(array $criteria, array $orderBy = null)
 * @method GnPostImages[]    findAll()
 * @method GnPostImages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GnPostImagesRepository extends ServiceEntityRepository
{
    protected $paginator;
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, GnPostImages::class);
        $this->paginator = $paginator;
    }

     public function get(GnPostImagesCategory $category, $number = 10)
    {
        return $this->createQueryBuilder('g')
            ->innerJoin('g.categories', 'gc', 'WITH', 'gc.id = :category')
            ->setParameter('category', $category->getId())
            ->andWhere('g.isApprouved = 1 AND g.isDeleted = 0')
            ->orderBy('g.postCreatedAt', 'DESC')
            ->setMaxResults($number)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return GnPostImages[] Returns an array of GnPostImages objects
     */
    public function getLatest()
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.isApprouved = 1 AND g.isDeleted = 0')
            ->orderBy('g.postCreatedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
 /**
     * @return GnPostImages[] Returns an array of GnPostImages objects
     */
    public function getRecentPosts($number = 10)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.isApprouved = 1 AND g.isDeleted = 0')
            ->orderBy('g.postCreatedAt', 'DESC')
            ->setMaxResults($number)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?GnPostImages
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * Récupération posts et url profil by id
     * @param int $categoryId
     * @return array
     */
    public function getPostsByCategory($categoryId)
    {
        $_post = GnPostImages::class;

        $_dql = "SELECT pst
                 FROM $_post pst
                 LEFT JOIN pst.categories ctg                
                 WHERE ctg.id = :id_category AND pst.isDeleted = 0 AND pst.isApprouved = 1
                 ORDER BY pst.postCreatedAt DESC ";

        $_query = $this->getEntityManager()->createQuery($_dql)->setMaxResults(3);
        $_query->setParameter('id_category', $categoryId);

        return !empty($_query->getResult()) ? $_query->getResult()[0] : [];
    }

    /**
     * Récupération posts et url profil by id
     * @param int $id
     * @return string
     */
    public function getAllPostParCategory($id)
    {
        $_post = GnPostImages::class;

        $_dql = "SELECT pst
                 FROM $_post pst
                 LEFT JOIN pst.categories ctg                
                 WHERE ctg.id = :id_category AND pst.isDeleted = 0 AND pst.isApprouved = 1
                 ORDER BY pst.postCreatedAt DESC";

        $_query = $this->getEntityManager()->createQuery($_dql)->setMaxResults(3);
        $_query->setParameter('id_category', $id);

        $_result = $_query->getResult();
        return  $_result;
    }

    /**
     * Récupération posts et url profil by id
     * @param int $id
     * @return string
     */
    public function getFullPostParCategory($id, $page)
    {
        $_post = GnPostImages::class;

        $_dql = "SELECT pst
                 FROM $_post pst
                 LEFT JOIN pst.categories ctg                
                 WHERE ctg.id = :id_category AND pst.isDeleted = 0 AND pst.isApprouved = 1
                 ORDER BY pst.postCreatedAt DESC";

        //$_query = $this->getEntityManager()->createQuery($_dql)->setMaxResults(3);
        $_query = $this->getEntityManager()->createQuery($_dql);
        $_query->setParameter('id_category', $id);

        $pagination = $this->paginator->paginate(
            $_query, /* query NOT result */
            $page, /*page number*/
            2 /*limit per page*/
        );

        //$_result = $_query->getResult();
        return  $pagination;
    }

    /**
     * Récupération posts et url profil by id
     * @param string $term
     * @param int $page
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getAllPostByTerm($term, $page): \Knp\Component\Pager\Pagination\PaginationInterface
    {
        $_post = GnPostImages::class;

        $_dql = "SELECT pst
                 FROM $_post pst
                 LEFT JOIN pst.categories ctg                
                 WHERE pst.isDeleted = 0 AND pst.isApprouved = 1
                 AND pst.postTitle LIKE :term
                     OR pst.postContent LIKE :term
                 ORDER BY pst.postCreatedAt DESC";

        $_query = $this->getEntityManager()->createQuery($_dql);
        $_query->setParameter('term', '%'.$term.'%');

        $pagination = $this->paginator->paginate(
            $_query, /* query NOT result */
            $page, /*page number*/
            2 /*limit per page*/
        );

        return  $pagination;
    }



    /**
     * Récupération posts et url profil by slug
     * @param string $slug
     * @return string
     */
    public function getFullPostParCategorySlug($slug, $page)
    {
        $_post = GnPostImages::class;

        $_dql = "SELECT pst
                 FROM $_post pst
                 LEFT JOIN pst.categories ctg                
                 WHERE ctg.postCategoryUrl LIKE :category_slug AND pst.isDeleted = 0 AND pst.isApprouved = 1
                 ORDER BY pst.postCreatedAt DESC";

        $_query = $this->getEntityManager()->createQuery($_dql);
        $_query->setParameter('category_slug', "%$slug%");

        $pagination = $this->paginator->paginate(
            $_query, /* query NOT result */
            $page, /*page number*/
            2 /*limit per page*/
        );

        //$_result = $_query->getResult();
        return  $pagination;
    }

    /**
     * Récupération posts et url profil by id
     * @param int $category_id
     * @param int $post_id
     * @return string
     */
    public function getRelatedPosts($category_id, $post_id)
    {
        $_post = GnPostImages::class;

        $_dql = "SELECT pst
                 FROM $_post pst
                 LEFT JOIN pst.categories ctg                
                 WHERE ctg.id = :id_category AND pst.isDeleted = 0 AND pst.isApprouved = 1 AND pst.id != :post_id
                 ORDER BY pst.postCreatedAt DESC";

        $_query = $this->getEntityManager()->createQuery($_dql)->setMaxResults(3);
        $_query->setParameter('id_category', $category_id);
        $_query->setParameter('post_id', $post_id);
        $_query->setMaxResults(3);

        $_result = $_query->getResult();
        return  $_result;
    }

    /**
     * Récupération post url profil by id
     * @param int $post_id
     * @return string
     */
    public function getRecurrentPostSlug($post_id, $next='next')
    {
        $_post = GnPostImages::class;
        $value = ($next == 'next') ? 'min(pstt.id) ' : 'max(pstt.id)';
        $comp = ($next == 'next') ? '> ' : '<';
        $_dql = "SELECT pst FROM $_post pst WHERE pst.id = (
                       SELECT $value FROM $_post pstt
                           WHERE pstt.id $comp :post_id 
                           AND pstt.isDeleted = 0 
                           AND pstt.isApprouved = 1 )";

        $_query = $this->getEntityManager()->createQuery($_dql)->setMaxResults(1);
        $_query->setParameter('post_id', $post_id);

        return (!empty($_query->getResult())) ? $_query->getResult()[0] : [];
    }

    /**
     * Récupération posts et url profil by id
     * @param int $category_id
     * @param int $post_id
     * @return int
     */
    public function getDefaultCategory($post_id)
    {
        $_post = GnPostImages::class;

        $_dql = "SELECT ctg.id
                 FROM $_post pst
                 LEFT JOIN pst.categories ctg                
                 WHERE pst.id = :id_post
                 ORDER BY ctg.id ASC";

        $_query = $this->getEntityManager()->createQuery($_dql)->setMaxResults(1);
        $_query->setParameter('id_post', $post_id);
        $_query->setMaxResults(1);
        $default_category = [];
        foreach ($_query->getResult() as $key => $category)
        {
            $default_category[] = $category['id'];
        }
        return  (is_array($default_category) && (!is_null($default_category[0]))) ? $default_category[0] : 0;
    }

    /**
     * Récupération posts et url profil by id
     * @param int $id
     * @return string
     */
    public function getPostByDrct($id)
    {
        $_post = GnPostImages::class;

        $_dql = "SELECT pst
                 FROM $_post pst
                 LEFT JOIN pst.categories ctg                
                 WHERE ctg.id = :id_category AND pst.isDeleted = 0 AND pst.isApprouved = 1
                 ORDER BY pst.postCreatedAt DESC";

        $_query = $this->getEntityManager()->createQuery($_dql)->setMaxResults(1);
        $_query->setParameter('id_category', $id);

        $_result = $_query->getResult();
        return  $_result;
    }

    public function getAllPostParUser($id)
    {

        $post = GnPostImages::class;
        $posts = $this->getEntityManager()->createQuery(
            "SELECT pst
            FROM $post pst
            LEFT JOIN pst.user usr
            WHERE usr.id = :user_id AND pst.isDeleted = 0 AND pst.isApprouved = 1"
        );
        $posts->setParameter('user_id', $id);

        //limiter par abonnement
        $posts->setMaxResults(2);

        // dd($posts->getResult());
        return $posts->getResult();



    }

    /**
     * Récupération posts et url profil by id
     * @param int $categoryId
     * @return array
     */
    public function getPostsByVideo()
    {
        $_post = GnPostImages::class;

        $_dql = "SELECT pst
                 FROM $_post pst                
                 WHERE pst.postVideo != '' AND pst.isDeleted = 0 AND pst.isApprouved = 1
                 ORDER BY pst.postCreatedAt DESC";

        $_query = $this->getEntityManager()->createQuery($_dql)->setMaxResults(3);
        return !empty($_query->getResult()) ? $_query->getResult() : [];
    }


    /**
     * Récupération posts et url profil by id
     * @param int $categoryId
     * @return array
     */
    public function getAllPostsByVideo($page)
    {
        $_post = GnPostImages::class;

        $_dql = "SELECT pst
                 FROM $_post pst                
                 WHERE pst.postVideo != '' AND pst.isDeleted = 0 AND pst.isApprouved = 1
                 ORDER BY pst.postCreatedAt DESC";

        $_query = $this->getEntityManager()->createQuery($_dql);

        $pagination = $this->paginator->paginate(
            $_query, /* query NOT result */
            $page, /*page number*/
            2 /*limit per page*/
        );

        //$_result = $_query->getResult();
        return  $pagination;
        //return !empty($_query->getResult()) ? $_query->getResult() : [];
    }
}
