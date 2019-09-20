<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class PasswordResetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class,
                                            ['mapped' => false,
                                             'label' => 'Mot de passe actuel :',
                                             'attr' => ['placeholder' => 'Entrez le mot de passe actuel...'],
                                             'constraints' => new UserPassword()
                                            ]
                )
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Nouveau mot de passe :',
                                     'attr' => ['placeholder' => 'Entrez le nouveau mot de passe...']],
                'second_options' => ['label' => 'Répetez le mot de passe :',
                                     'attr' => ['placeholder' => 'Repetez le mot de passe...']],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function getName()
    {
        return 'change_passwd';
    }
}
