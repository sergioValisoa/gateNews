<?php
namespace App\Form;

use App\Entity\GnPostImages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GnPostImagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile',VichImageType::class, [
                'required' => false,
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern'  => 'thumbnail_list',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GnPostImages::class,
        ]);
    }
}