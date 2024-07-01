<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('oldPassword',PasswordType::class,$this->getConfiguration('Entrer de votre mdp actuel',''))
        ->add('newPassword',PasswordType::class,$this->getConfiguration('Entrer de votre nouveau mdp',''))
        ->add('confirmPassword',PasswordType::class,$this->getConfiguration('Confirmation de votre nouveau mdp',''))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
