<?php

namespace Common\UserBundle\Form;

use Common\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AccountSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'label' =>  'Nick',
                'required'  =>  false
            ))
            ->add('avatarFile', FileType::class, array(
                'label' =>  'Zmień avatar',
                'required'  =>  false
            ))
            ->add('submit', SubmitType::class, array(
                'label' =>  'Zapisz zmiany'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    =>  User::class,
            'validation_groups' =>  array('Default', 'ChangeDetails')
        ));
    }

    public function getName()
    {
        return 'accountSettings';
    }
}
