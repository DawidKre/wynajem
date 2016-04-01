<?php

namespace Common\UserBundle\Form;

use Common\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'label' =>  'Nazwa użytkownika:'
            ))
            ->add('email', EmailType::class, array(
                'label' =>  'Adres E-mail'
            ))

            ->add('plainPassword', RepeatedType::class, array(
                'type'  =>  PasswordType::class,
                'first_options' =>  array(
                    'label' =>  'Hasło'
                ),
                'second_options'    =>  array(
                    'label' =>  'Powtórz hasło'
                )
            ))
            ->add('save', SubmitType::class, array(
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
