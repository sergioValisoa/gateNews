<?php

namespace App\Form;

use App\Entity\NousContacter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class NousContacterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,[ 'attr' => ['class' => 'form-control mb-3']])
            ->add('prenom',TextType::class,[ 'attr' => ['class' => 'form-control mb-3'],'required'  => false])
            ->add('email',EmailType::class,[ 'attr' => ['class' => 'form-control mb-3']])
            ->add('message',TextareaType::class,[ 'attr' => ['class' => 'form-control mb-3', 'rows' => 5]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NousContacter::class,
        ]);
    }
}
