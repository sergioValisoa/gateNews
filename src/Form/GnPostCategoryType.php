<?php

namespace App\Form;

use App\Entity\GnPostCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class GnPostCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categoryTitle',TextType::class,
                [ 'attr' => ['class' => 'form-control mb-3'], 'label' => 'Titre de la catégorie'])
            ->add('categoryDescription', TextareaType::class,
                [ 'attr' => ['class' => 'form-control mb-3', 'rows' => 6], 'label' => 'Description de la catégorie'])
            ->add('metaKey',TextType::class,
                [ 'attr' => ['class' => 'form-control'], 'label' => 'Mots clés'])
            ->add('metaDescription',TextType::class,[
                    'attr' => ['class' => 'form-control'], 'label' => 'Description'])
            ->add('postCategoryUrl',TextType::class,
                [ 'attr' => ['class' => 'form-control'], 'label' => 'Url de la catégorie', 'required' => true])
            ->add('categoryImageFile', VichImageType::class,[
                'label' => 'Image de la catégorie',
                'required'  => false,
                'allow_delete'  => true,
                'download_label'  => 'Télécharger',
                'download_uri'  => true,
                'image_uri'  => true,
                'imagine_pattern'  => 'thumbnail_list',
                'asset_helper'  => true,
                'attr' => ['class' => 'form-control mb-3',
                    'style' => "border:none!important"]
            ])
            ->add('premium', CheckboxType::class, [
                'label' => 'Premium',
                'required' => false, 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GnPostCategory::class,
        ]);
    }
}
