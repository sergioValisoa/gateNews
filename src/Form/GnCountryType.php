<?php

namespace App\Form;

use App\Entity\GnCountry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GnCountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add(                             
                'countryName', TextType::class,[                 
                    'attr' => ['class' => 'form-control']
                    ,'label' => 'Nom du pays'  ]         
            )
        ->add('imageFile', VichImageType::class,[
                'label' => 'drapeau',
                'required'  => false,
                'allow_delete'  => true,
                'download_label'  => 'Télécharger',
                'download_uri'  => true,
                'image_uri'  => true,
                'asset_helper'  => true,
                'imagine_pattern' => 'thumbnail_list',
                'attr' => ['class' => 'form-control mb-3',
                 'style' => "border:none!important"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GnCountry::class,
        ]);
    }
}
