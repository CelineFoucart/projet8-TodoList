<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'disabled' => true,
                'help' => 'Pour modifier votre identifiant, contactez un administrateur.',
            ])
            ->add('email', EmailType::class)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => false,
                'help' => 'Laissez vide pour ne pas modifier le mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Length(['min' => 6, 'max' => 4096]),
                ],
                'first_options' => ['label' => 'password.first'],
                'second_options' => ['label' => 'password.second'],
            ])
        ;
    }
}
