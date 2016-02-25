<?php

namespace Common\UserBundle\Form;

use Common\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'label' =>  'Nazwa użytkownika:'
            ))
            ->add('email', 'email', array(
                'label' =>  'Adres E-mail'
            ))

            ->add('plainPassword', 'repeated', array(
                'type'  =>  'password',
                'first_options' =>  array(
                    'label' =>  'Hasło'
                ),
                'second_options'    =>  array(
                    'label' =>  'Powtórz hasło'
                )
            ))
            ->add('save', 'submit', array(
                'label' =>  'Zarejestruj'
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' =>  User::class,
            'validation_groups' => array('Default', 'Registration')

        ));
    }

    public function getName()
    {
        return 'userRegister';
    }
}
