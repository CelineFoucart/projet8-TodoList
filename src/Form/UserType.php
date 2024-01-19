<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $options['data'];
        if ($data instanceof User) {
            $id = $data->getId();
        } else {
            $id = null;
        }
        
        $builder
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => (null === $id) ? true : false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Length(['min' => 6, 'max' => 4096]),
                ],
                'first_options' => ['label' => 'password.first'],
                'second_options' => ['label' => 'password.second'],
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_USER' => 'ROLE_USER',
                ],
                'required' => true,
                'multiple' => true
            ])
            ->add('isVerified', CheckboxType::class, ['required' => false])
        ;
    }
}
