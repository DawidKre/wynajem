<?php

namespace Wynajem\AdminBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class CategoryDeleteType extends AbstractType
{

    private $category;

    function __construct($category) {
        $this->category = $category;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $Category = $this->category;

        $builder
            ->add('setNull', CheckboxType::class, array(
                'label' => 'Ustaw wszystkie posty bez kategorii'
            ))
            ->add('newCategory', EntityType::class, array(
                'label' => 'Wybierz nową kategorię dla postów',
                'empty_value' => 'Wybierz kategorię',
                'class' => 'Wynajem\BlogBundle\Entity\Category',
                'property' => 'name',
                'query_builder' => function(EntityRepository $er) use ($Category){
                    return $er->createQueryBuilder('c')
                        ->where('c.id != :categoryId')
                        ->setParameter('categoryId', $Category->getId());
                }
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Usuń kategorię'
            ));
    }


    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'deleteCategory';
    }
}
