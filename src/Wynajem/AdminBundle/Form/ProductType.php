<?php

namespace Wynajem\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Nazwa',
                'attr' => array(
                    'placeholder' => 'Nazwa'
                )
            ))
//            ->add('slug', 'text', array(
//                'label' => 'Alias',
//                'attr' => array(
//                    'placeholder' => 'Alias'
//                )
//            ))
            ->add('description', 'ckeditor', array(
                'label' => 'Treść'
            ))
            ->add('priceForHour', NumberType::class, array(
                'label' => 'Cena za godzine'

            ))
            ->add('priceForDay', NumberType::class, array(
                'label' => 'Cena za dzień'
            ))
            ->add('available', CheckboxType::class, array(
                'label' => 'Dostępność',
                'value' => true
            ))
//            ->add('content', 'textarea', array(
//                'label' => 'Treść'
//            ))
            ->add('thumbnailFile', 'file', array(
                'label' => 'Miniaturka'
            ))
//            ->add('publishedDate', 'datetime', array(
//                'label' => 'Data publikacji',
//                'date_widget' => 'single_text', //pokazać też bez
//                'time_widget' => 'single_text'
//            ))
            ->add('category', 'entity', array(
                'label' => 'Kategoria',
                'class' => 'Wynajem\BlogBundle\Entity\Category',
                'property' => 'name',
                'empty_value' => 'Wybierz kategorię'
            ))
            ->add('tags', 'entity', array(
                'label' => 'Tagi',
                'multiple' => true,
                'class' => 'Wynajem\BlogBundle\Entity\Tag',
                'property' => 'name',
                'attr' => array(
                    'placeholder' => 'Dodaj tagi'
                )
            ))
            ->add('save', 'submit', array(
                'label' => 'Zapisz'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    =>  'Wynajem\BlogBundle\Entity\Product'
        ));

    }

    public function getName()
    {
        return 'product';
    }
}
