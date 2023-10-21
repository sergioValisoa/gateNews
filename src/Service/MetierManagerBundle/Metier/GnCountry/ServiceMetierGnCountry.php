<?php

namespace App\Service\MetierManagerBundle\Metier\GnCountry;

use App\Entity\GnCountry;
use App\Service\MetierManagerBundle\Utils\ServiceName;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ServiceMetierGnCountry
 * @package App\Service\MetierManagerBundle\Metier\GnCountry
 */
class ServiceMetierGnCountry
{
    private $_entity_manager;
    private $_container;

    /**
     * ServiceMetierGnCountry constructor.
     * @param EntityManagerInterface $_entity_manager
     * @param ContainerInterface $_container
     */
    public function __construct(EntityManagerInterface $_entity_manager, ContainerInterface $_container)
    {
        $this->_entity_manager = $_entity_manager;
        $this->_container      = $_container;
    }

    /**
     * Ajout country
     * @param GnCountry $country
     * @param Object $_form
     * @return GnCountry
     */
    public function addCountry($country, $_form)
    {
        // Récupérer manager
        $utils_manager = $this->_container->get(ServiceName::SRV_METIER_UTILS);
        return $utils_manager->saveEntity($country, 'new');
    }

    /**
     * Modification country
     * @param GnCountry $country
     * @param Object $_form
     * @return GnCountry
     */
    public function updateCountry($country, $_form)
    {
        // Récupérer manager
        $_utils_manager = $this->_container->get(ServiceName::SRV_METIER_UTILS);
        //$_seo_manager   = $this->_container->get(ServiceName::SRV_METIER_SEO);

        // Traitement image seo
        /*$_image_seo = $_form['csnSeo']['seoImageUrl']->getData();
        // S'il y a un nouveau fichier ajouté, on supprime l'ancien fichier puis on enregistre ce nouveau
        if ($_image_seo) {
            $_seo = $country->getCsnSeo();
            $_seo_manager->deleteOnlyImage($_seo);
            $_seo_manager->addImage($_seo, $_image_seo);
        }*/

        return $_utils_manager->saveEntity($country, 'new');
    }
}
