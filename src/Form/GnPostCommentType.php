<?php

namespace App\Form;

use App\Entity\GnPostComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class GnPostCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('postCommentContent',TextareaType::class,[
                    'attr' => ['class' => 'form-control', 'rows' => 5],'label' => 'Commentaire'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GnPostComment::class,
        ]);
    }
}
