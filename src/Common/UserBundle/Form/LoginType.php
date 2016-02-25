<?php

namespace Common\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'label' =>  'Nazwa użytkownika:'
            ))
            ->add('password', 'password', array(
                'label' =>  'Hasło:'
            ))
            ->add('remember_me', 'checkbox', array(
                'label' =>  'Zapamiętaj mnie'
            ))
            ->add('save', 'submit', array(
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
