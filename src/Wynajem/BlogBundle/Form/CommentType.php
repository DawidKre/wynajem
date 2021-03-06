<?php

namespace Wynajem\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wynajem\BlogBundle\Entity\Comment;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', 'textarea', array(
                'label' =>  'Komentarz',
                'attr'  =>  array('class' => 'form-control')
            ))
            ->add('save', 'submit', array(
                'label' =>  'Wyślij',
                'attr'  =>  array('class' => 'btn btn-primary pull-right')
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    =>  'Wynajem\BlogBundle\Entity\Comment'
        ));

    }

    public function getName()
    {
        return 'comment';
    }
}
