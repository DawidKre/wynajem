<?php

namespace Common\UserBundle\Form;

use Common\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', 'password', array(
                'label' =>  'Aktualne hasło',
                'mapped'  =>  false,
                'constraints'   =>  array(
                    new UserPassword(array(
                        'message' => 'Podano błędne aktualne hasło użytkownika'
                    ))
                )
            ))
            ->add('plainPassword', 'repeated', array(
                'type' =>  'password',
                'first_options'  =>  array(
                    'label' =>  'Nowe hasło'
                ),
                'second_options'    =>  array(
                    'label' =>  'Powtórz hasło'
                )
            ))
            ->add('submit', 'submit', array(
                'label' =>  'Zapisz hasło'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    =>  User::class,
            'validation_groups' =>  array('Default', 'ChangePassword')
        ));
    }

    public function getName()
    {
        return 'changePassword';
    }
}
