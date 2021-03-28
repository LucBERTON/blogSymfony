<?php

namespace App\Form;

use App\Entity\Commentaires;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //le 'attr' => ['class' => 'form-control']  est pour une mise en forme automatique via bootstrap
            ->add('pseudo', TextType::class, [ 
                'attr' => [ 
                    'class' => 'form-control'
                ]
            ])
            ->add('email', EmailType::class, [ 
                'attr' => [ 
                    'class' => 'form-control'
                ]
            ])
            ->add('contenu', TextareaType::class, [ 
                'attr' => [ 
                    'class' => 'form-control'
                ]
            ])
            ->add('rgpd', CheckboxType::class, [
                'label'=> 'J\'accepte que mes données soient conservées...(RGPD)'
            ])
            ->add('Envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commentaires::class,
        ]);
    }
}
