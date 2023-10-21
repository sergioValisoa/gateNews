<?php

namespace App\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use App\Entity\GnPost;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GnPostType extends AbstractType
{
    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker=null;
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker=$authorizationChecker;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('postTitle', TextType::class, [
                'attr' => ['class' => 'form-control postTitle']
            ])
            ->add('postContent', CKEditorType::class, array(
                'config' => array('toolbar' => 'full', 'scayt_autoStartup' => true),
                
            ))
            ->add('categories',  EntityType::class, array(
                'label'         => 'Catégorie',
                'class'         => 'App\Entity\GnPostCategory',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('ctg')
                        ->orderBy('ctg.id', 'DESC');
                },
                'choice_label'  => 'categoryTitle',
                'multiple'      => true,
                'expanded'      => false,
                'attr' => ['class' => 'form-control', 'data-placeholder' => '- Séléctionner une ou plusieurs catégories ici-'],
                'required'      => true,
                'placeholder'   => '- Séléctionner les catégories -'
            ))
            ->add('metaKey', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('postUrl', TextType::class, [
                'attr' => ['class' => 'form-control postUrl', 'required' => true]
            ])
            ->add('metaDescription', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image à la une',
                'required'  => false,
                'allow_delete'  => true,
                'download_label'  => 'Télécharger',
                'download_uri'  => true,
                'image_uri'  => true,
                'imagine_pattern'  => 'thumbnail_list',
                'asset_helper'  => true,
                'attr' => [
                    'class' => 'form-control mb-3 imageFileClass',
                    'style' => "border:none!important"
                ]
            ])
            ->add('postImages', CollectionType::class, [
                'entry_type' => GnPostImagesType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true
            ])
            ->add('videoFile', VichFileType::class, [
                'label' => "Video de l'article",
                'required'  => false,
                'allow_delete'  => true,
                'download_label'  => 'Télécharger',
                'download_uri'  => true,
                'asset_helper'  => true,
                'attr' => [
                    'class' => 'form-control mb-3 videoFileClass',
                    'style' => "border:none!important"
                ]
            ]);
            if($this->authorizationChecker->isGranted('ROLE_ADMIN'))
            {
                $builder->add('isApprouved',CheckboxType::class,["label"=>"Approuver ?","required"=>false]);
            }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GnPost::class,
            //"allow_extra_fields" => true,
        ]);
    }
}
