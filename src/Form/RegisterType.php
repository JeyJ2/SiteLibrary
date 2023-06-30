<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
            'label' => 'Votre mail',
            'attr' => ['placeholder' => 'Veuillez saisir votre email']
            ])
            ->add('password', RepeatedType::class,[
                'constraints'=> new Length ([
                    'min' => 4,
                    'max' => 30,
                    'minMessage' => 'Votre mot de passe doit contenir au moins 4 caractères'
                ]),
                'type'=> PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'required' => true,
                'first_options' => [
                    'label' => 'Entrez Votre mot de passe',
                    'attr' => ['placeholder' => 'Entrez votre mot de passe']
                ],
                'second_options' => [
                    'label' => 'Confirmez Votre mot de passe',
                    'attr' => ['placeholder' => 'Confirmez votre mot de passe']
                ]
            ])
            ->add('lastname', TextType::class,[
            'label' => 'Votre nom',
            'attr' => ['placeholder' => 'Veuillez saisir votre nom']
            ])
            ->add('firstname', TextType::class,[
            'label' => 'Votre prénom', 
            'attr'=> ['placeholder' => 'Veuillez saisir votre prénom']
            ])
            ->add('avatar', TextType::class,[
            'label' => 'Votre avatar',
            'attr' => ['placeholder' => 'Veuillez saisir votre avatar']
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
