<?php

namespace App\Service\MetierManagerBundle\Metier\GnPost;

use App\Entity\GnPost;
use App\Repository\GnPostRepository;
use App\Service\MetierManagerBundle\Utils\ServiceName;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use function Symfony\Component\String\u;

/**
 * Class ServiceMetierGnPost
 * @package App\Service\MetierManagerBundle\Metier\GnPost
 */
class ServiceMetierGnPost
{
    private $_entity_manager;
    private $_container;
    private $_helper;
    private $_cacheManager;

    /**
     * ServiceMetierGnPost constructor.
     * @param EntityManagerInterface $_entity_manager
     * @param ContainerInterface $_container
     */
    public function __construct(EntityManagerInterface $_entity_manager, ContainerInterface $_container, UploaderHelper $helper, CacheManager $cacheManager)
    {
        $this->_entity_manager = $_entity_manager;
        $this->_container      = $_container;
        $this->_helper          = $helper;
        $this->_cacheManager    = $cacheManager;
    }

    /**
     * Ajout post
     * @param GnPost $post
     * @param Object $_form
     * @return GnPost
     */
    public function addPost($post, $request, $user,$categories=null)
    {
        
        // Récupérer manager
        $securityContext = $this->_container->get('security.authorization_checker');
        // Récupérer manager
        $utils_manager = $this->_container->get(ServiceName::SRV_METIER_UTILS);
        
        if (!empty($categories)) {
            foreach ($categories as $category)
            {
                $post->addCategory($category);
            }
        }
        //$post->setUser($user[0]);
        $post->setUser($user);
        $post->setPostCreatedAt(new \DateTime());
         if ($securityContext->isGranted('ROLE_ADMIN')) {
            //l'utilisateur à les droits admin
            $post->setIsApprouved(1);
        }
        else
        {
            $post->setIsApprouved(0);
        }
        $post->setIsDeleted(0);
        $post->setIsCommented(0);
        $postImages = $post->getPostImages();
        foreach($postImages as $key => $postImage){
            $postImage->setPost($post);
            $this->_entity_manager->persist($postImage);
            //$this->_entity_manager->flush();
            $postImages->set($key,$postImage);
        }
        //dd($postImages);
        return $utils_manager->saveEntity($post, 'new');
    }

    /**
     * Modification post
     * @param GnPost $post
     * @param Object $_form
     * @return GnPost
     */
    public function updatePost($post, $_form)
    {
        // Récupérer manager
        $_utils_manager = $this->_container->get(ServiceName::SRV_METIER_UTILS);
        $_seo_manager   = $this->_container->get(ServiceName::SRV_METIER_SEO);

        // Traitement image seo
        $_image_seo = $_form['csnSeo']['seoImageUrl']->getData();
        // S'il y a un nouveau fichier ajouté, on supprime l'ancien fichier puis on enregistre ce nouveau
        if ($_image_seo) {
            $_seo = $post->getCsnSeo();
            $_seo_manager->deleteOnlyImage($_seo);
            $_seo_manager->addImage($_seo, $_image_seo);
        }

        return $_utils_manager->saveEntity($post, 'new');
    }

    /**
     * Retrieve post array list
     * @param $_page
     * @param $_nb_max_page
     * @param $_search
     * @param $_order_by
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function postListArray($_page, $_nb_max_page, $_search, $_order_by)
    {
        $_order_by = $_order_by ? $_order_by : "pst.id DESC";

        $_post_response = GnPost::class;

        $_dql = "SELECT pst.id,
						pst.postTitle,
						pst.postContent,
                        pst.postPhotos,
                        usr.userFullname,
                        pst.postCreatedAt,
                        pst.postUrl
                    FROM $_post_response pst
                    JOIN pst.user usr
                    WHERE
                        (pst.postTitle LIKE :search 
                        OR pst.postContent LIKE :search) AND pst.isDeleted = 0
                 ORDER BY $_order_by";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('search', "%$_search%")
            ->setFirstResult($_page)
            ->setMaxResults($_nb_max_page);
        $posts = [];
        foreach ($_query->getResult() as $key => $post) {
            $gnPostRepository = $this->_entity_manager->getRepository('App:GnPost');
            $post_entity                        = $gnPostRepository->findOneBy(['id' => $post['id']]);
            $imageString                        = $this->_helper->asset($post_entity, 'imageFile');
            $imageString                        = (!is_null($post['postPhotos'])) ? $this->_cacheManager->getBrowserPath($imageString, 'thumbnail_list') : '';
            $posts[$key]['pst.id']              = $post['id'];
            $posts[$key]['pst.postTitle']       = $post['postTitle'];
            $posts[$key]['pst.postContent']     = u($post['postContent'])->truncate(50, '...');
            $posts[$key]['ctg.categoryTitle']   = implode("; ", $this->getPostCategories($post['id']));
            $posts[$key]['pst.postPhotos']      = $imageString;
            $posts[$key]['usr.userFullname']    = $post['userFullname'];
            $posts[$key]['pst.postCreatedAt']   = $post['postCreatedAt']->format('d/m/Y H:i:s');
            $posts[$key]['pst.postUrl']         = $post['postUrl'];
            $posts[$key]['pst.idPost']          = $post_entity->getId();
        }

        return [$posts, $this->countPost()];
    }

    /**
     * post categories
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPostCategories($post_id)
    {
        $_post = GnPost::class;

        $_dql = "SELECT ctg.categoryTitle FROM $_post pst
                 INNER JOIN pst.categories ctg WHERE pst.id = :post_id";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('post_id', $post_id);
        $categories = [];
        foreach ($_query->getResult() as $key => $value)
        {
            $categories[] = $value['categoryTitle'];
        }
        return $categories;
    }

    /**
     * count post
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countPost()
    {
        $_post = GnPost::class;

        $_dql = "SELECT COUNT (pst) as nbTotal
 					FROM $_post pst";

        $_query = $this->_entity_manager->createQuery($_dql);

        return $_query->getOneOrNullResult()['nbTotal'];
    }

    /**
     * update post meta
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updatePostMeta($id, $newContent)
    {
        $_post = GnPost::class;

        $_dql = "UPDATE $_post pst SET pst.metaDescription = :new_content
 				 WHERE pst.id = :pst_id	";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('pst_id', $id);
        $_query->setParameter('new_content', $newContent);
        return $_query->execute();
    }
}
