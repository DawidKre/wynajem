<?php

namespace Common\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'label' =>  'Nazwa użytkownika:'
            ))
            ->add('password', PasswordType::class, array(
                'label' =>  'Hasło:'
            ))
            ->add('remember_me', CheckboxType::class, array(
                'label' =>  'Zapamiętaj mnie',
                'required'  => false
            ))
            ->add('save', SubmitType::class, array(
                'label' =>  'Zaloguj'
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'login';
    }
}
