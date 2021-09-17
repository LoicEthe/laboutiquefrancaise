<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname',TextType::class,[
                'label'=> 'Prénom',
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30
                ]), // contrainte
                'attr' => [
                    'placeholder' => 'Merci de saisir votre prénom'
                ]
            ])
            ->add('lastname',TextType::class,[
                'label'=> 'Nom de famille',
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30]),
                'attr' => [
                    'placeholder' => 'Merci de saisir votre nom de famille'
                ]
            ])
            ->add('email',EmailType::class,[
                'label' => 'Email',
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 60]),
                'attr' =>[
                    'placeholder' => 'Merci de saisir votre adresse email'
                ]
            ])
            ->add('password',RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'label' => 'Mot de passe',
                'required' => true,
                'first_options' => ['label' => "Mot de passe"],
                'second_options' => ['label' => "Confirmez votre mot de passe"]
            ])

            ->add('submit',SubmitType::class,[
                'label' => "S'inscrire"
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
