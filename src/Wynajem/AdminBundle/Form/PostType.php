<?php

namespace Wynajem\AdminBundle\Form;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'label' => 'Tytuł',
                'attr' => array(
                    'placeholder' => 'Tytuł'
                )
            ))
            ->add('content', 'ckeditor', array(
                'label' => 'Treść'
            ))

            ->add('thumbnailFile', FileType::class, array(
                'label' => 'Miniaturka'
            ))
            ->add('publishedDate', DateTimeType::class, array(
                'label' => 'Data publikacji',
                'date_widget' => 'single_text', //pokazać też bez
                'time_widget' => 'single_text',
                'data'   => new \DateTime()
            ))
            ->add('category', EntityType::class, array(
                'label' => 'Kategoria',
                'class' => 'Wynajem\BlogBundle\Entity\Category',
                'property' => 'name',
                'empty_value' => 'Wybierz kategorię'
            ))
            ->add('tags', EntityType::class, array(
                'label' => 'Tagi',
                'multiple' => true,
                'class' => 'Wynajem\BlogBundle\Entity\Tag',
                'property' => 'name',
                'attr' => array(
                    'placeholder' => 'Dodaj tagi'
                )
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Zapisz'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    =>  'Wynajem\BlogBundle\Entity\Post'
        ));

    }

    public function getName()
    {
        return 'post';
    }
}
