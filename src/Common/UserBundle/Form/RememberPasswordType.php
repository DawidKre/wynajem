<?php

namespace Common\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RememberPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'Adres E-mail',
                'constraints' => array(
                    new Assert\Email(),
                    new Assert\NotBlank()
                )
            ))
            ->add('submit', 'submit', array(
                'label' => 'Przypomnij hasło'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'rememberPassword';
    }
}
