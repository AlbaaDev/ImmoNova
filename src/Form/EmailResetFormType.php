<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailResetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class,
                [   'mapped' => false,
                    'attr' => ['placeholder' => 'Entrez l\'email actuel...'],
                    'label' => 'Email :'

                ])
            ->add('newEmail', RepeatedType::class, [
                'type' => EmailType::class,
                'invalid_message' => 'Les emails doivent correspondre.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Nouvel email :', 'attr' => ['placeholder' => 'Entrez le nouvelle email...']],
                'second_options' => ['label' => 'Repetez l\'email :', 'attr' => ['placeholder' => 'Repetez l\'email...']],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
