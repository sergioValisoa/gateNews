<?php

namespace App\Service\MetierManagerBundle\Metier\GnAccount;

use App\Entity\GnUser;
use App\Service\MetierManagerBundle\Utils\ServiceName;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ServiceMetierGnAccountAdmin
 * @package App\Service\MetierManagerBundle\Metier\GnAccountAdmin
 */
class ServiceMetierGnAccount
{
    private $_entity_manager;
    private $_container;

    /**
     * ServiceMetierGnAccountAdmin constructor.
     * @param EntityManagerInterface $_entity_manager
     * @param ContainerInterface $_container
     */
    public function __construct(EntityManagerInterface $_entity_manager, ContainerInterface $_container)
    {
        $this->_entity_manager = $_entity_manager;
        $this->_container      = $_container;
    }



    /**
     * post categories
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAllUsersEmail()
    {
        $_user = GnUser::class;

        $_dql = "SELECT usr.userEmail, usr.userFullname FROM $_user usr WHERE usr.isDeleted = 0";

        $_query = $this->_entity_manager->createQuery($_dql);
        $users = [];
        foreach ($_query->getResult() as $key => $value)
        {
            $users[$key]['userEmail'] = $value['userEmail'];
            $users[$key]['userFullName'] = $value['userFullname'];
        }
        return $users;
    }

    /**
     * Ajout user
     * @param GnUser $user
     * @param Object $_form
     * @return GnUser
     */
    public function addAccount($user, $_form, $request,$pwd_encode, $roles=null)
    {
        // Récupérer manager
        $utils_manager = $this->_container->get(ServiceName::SRV_METIER_UTILS);
        if (!empty($roles)) {
            foreach ($roles as $role)
            {
                $user->addGnRole($role);
            }
        }

        // Traitement du photo
        /*$_img_photo = $_form['usrPhoto']->getData();
        if ($_img_photo) {
            $_user_upload_manager = $this->_container->get(ServiceName::SRV_METIER_USER_UPLOAD);
            $_user_upload_manager->upload($user, $_img_photo);
        }
        */
        $pwd = $request->request->get('gn_admin_account')['userPassword'];
        $user->setUserPassword($pwd_encode->encodePassword($user,$pwd));
        $user->setCreatedAt(new \DateTime());
        $user->setConfirmAt(new \DateTime());
        return $utils_manager->saveEntity($user, 'new');
    }

    /**
     * Modification user
     * @param GnUser $user
     * @param Object $_form
     * @return GnUser
     */
    public function updateAccount($user, $_form)
    {
        // Récupérer manager
        $_utils_manager = $this->_container->get(ServiceName::SRV_METIER_UTILS);
        $_seo_manager   = $this->_container->get(ServiceName::SRV_METIER_SEO);

        // Traitement image seo
        $_image_seo = $_form['csnSeo']['seoImageUrl']->getData();
        // S'il y a un nouveau fichier ajouté, on supprime l'ancien fichier puis on enregistre ce nouveau
        if ($_image_seo) {
            $_seo = $user->getCsnSeo();
            $_seo_manager->deleteOnlyImage($_seo);
            $_seo_manager->addImage($_seo, $_image_seo);
        }

        return $_utils_manager->saveEntity($user, 'new');
    }
}
