<?php

namespace Common\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ManageUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'label' => 'Nick',
                'attr' => array(
                    'placeholder' => 'Nick'
                )
            ))
            ->add('email', EmailType::class, array(
                'label' => 'E-mail',
                'attr' => array(
                    'placeholder' => 'E-mail'
                )
            ))
            ->add('accountExpired', CheckboxType::class, array(
                'label' => 'Konto wygasło'
            ))
            ->add('accountLocked', CheckboxType::class, array(
                'label' => 'Konto zablokowane'
            ))
            ->add('credentialsExpired', CheckboxType::class, array(
                'label' => 'Dane uwierzytelniające wygasły'
            ))
            ->add('enabled', CheckboxType::class, array(
                'label' => 'Konto aktywowane'
            ))
            ->add('roles', ChoiceType::class, array(
                'label' => 'Role',
                'multiple' => true,
                'choices' => array(
                    'ROLE_USER' => 'Użytkownik',
                    'ROLE_EDITOR' => 'Redaktor',
                    'ROLE_ADMIN' => 'Administrator',
                    'ROLE_SUPER_ADMIN' => 'Super Administrator'
                )
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Common\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'manageUser';
    }
}
