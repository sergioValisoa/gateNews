<?php

namespace App\Service\MetierManagerBundle\Metier\Upload;

use Doctrine\ORM\EntityManager;
use App\Service\MetierManagerBundle\Utils\PathName;
use App\Entity\GnUser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UploadManager
 * @package App\Service\MetierManager\Metier\Utils
 */
class UploadManager
{
    protected $_em;
    protected $_web_root;

    public function __construct(EntityManager $_em, $_root_dir)
    {
        $this->_em       = $_em;
        $this->_web_root = realpath($_root_dir . '/public');
    }
        
    /**
     * Suppression fichier (fichier avec entité)
     * @param integer $_id identifiant utilisateur
     * @return array
     */
    public function deleteImageById($_id)
    {
        $_user = $this->_em->getRepository('App:GnUser')->find($_id);
        
        if ($_user) {
            try {
                $_path = $this->_web_root.$_user->getUsrPhoto();
        
                // Suppression du fichier
                @unlink($_path);

                // Suppression dans la base
                $_user->setUsrPhoto(null);
                $this->_em->persist($_user);
                $this->_em->flush();

                return array(
                    'success' => true
                );
            } catch (\Exception $_exc) {

                return array(
                    'success' => false,
                    'message' => $_exc->getTraceAsString()
                );
            }
        } else {

            return array(
                'success' => false,
                'message' => 'Image not found in database'
            );
        }
    }

    /**
     * Suppression fichier (uniquement le fichier)
     * @param integer $_id identifiant utilisateur
     * @return array
     */
    public function deleteOnlyImageById($_id)
    {
        $_user = $this->_em->getRepository('App:GnUser')->find($_id);

        if ($_user) {
            $_path = $this->_web_root.$_user->getUsrPhoto();

            // Suppression du fichier
            @unlink($_path);
        }
    }
    
    /**
     * Upload fichier
     * @param GnUser $_user
     * @param file $_image
     */
    public function upload(GnUser $_user, $_image) {
        try {
            $_filename_image = md5(uniqid()).'.'.$_image->guessExtension();
            $_uri_file       = PathName::UPLOAD_IMAGE_USER . $_filename_image;
            $_dir            = $this->_web_root . PathName::UPLOAD_IMAGE_USER;
            $_image->move(
                $_dir,
                $_filename_image
            );
            $_user->setUsrPhoto($_uri_file);
        } catch (\Exception $_exc) {
            throw new NotFoundHttpException("Erreur survenue lors de l'upload fichier");
        }
    }    
}