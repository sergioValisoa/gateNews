<?php

namespace App\Form;

use App\Entity\GnUser;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GnAdminAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('gnRoles',  EntityType::class, array(
                'label'         => 'Rôles',
                'class'         => 'App\Entity\GnRole',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('rl')
                        ->orderBy('rl.id', 'DESC');
                },
                'choice_label'  => 'roleName',
                'multiple'      => true,
                'expanded'      => false,
                'attr' => ['class' => 'form-control', 'data-placeholder' => '- Séléctionner un ou plusieurs rôles ici-'],
                'required'      => true,
                'placeholder'   => '- Séléctionner les rôles -'
            ))
            ->add('userName',TextType::class,[ 'attr' => ['class' => 'form-control mb-3'], 'label' => 'Identifiant'])
            ->add('userFullname',TextType::class,[ 'attr' => ['class' => 'form-control mb-3'], 'required' => false, 'label' => 'Nom complet'])
            ->add('userAdress',TextType::class,[ 'attr' => ['class' => 'form-control mb-3'], 'required' => false, 'label' => 'Adresse'])
            ->add('userEmail',EmailType::class,[ 'attr' => ['class' => 'form-control mb-3'], 'label' => 'Adresse email'])
            ->add('userPassword',PasswordType::class,[ 'attr' => ['class' => 'form-control mb-3', 'minlength' => 6], 'label' => 'Mot de passe'])
            ->add('imageFile', VichImageType::class,[
                'label' => 'Photo de profil',
                'required'  => false,
                'allow_delete'  => true,
                'download_label'  => 'Télécharger',
                'download_uri'  => true,
                'image_uri'  => true,
                'asset_helper'  => true,
                'imagine_pattern' => 'thumbnail_list',
                'attr' => ['class' => 'form-control mb-3',
                 'style' => "border:none!important"]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GnUser::class,
        ]);
    }
}
