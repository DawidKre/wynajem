<?php

namespace Wynajem\AdminBundle\Form;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Nazwa',
                'attr' => array(
                    'placeholder' => 'Nazwa'
                )
            ))
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
            ->add('thumbnailFile', FileType::class, array(
                'label' => 'Miniaturka'
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
            ->add('save',SubmitType::class, array(
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
