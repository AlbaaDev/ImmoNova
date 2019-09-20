<?php

namespace App\Form;

use App\Entity\Property;
use App\Form\DataTransformer\ArrayToImageTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{

    private $transformer;

    public function __construct(ArrayToImageTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('surface')
            ->add('rooms')
            ->add('bedrooms')
            ->add('floor')
            ->add('price')
            ->add('heat', ChoiceType::class,
                [
                    'choices' => array_flip(Property::HEAT),
                ])
            ->add('type', ChoiceType::class,
                [
                    'choices' => array_flip(Property::MODE)
                ]
            )
            ->add('mode', ChoiceType::class,
                [
                    'choices' => array_flip(Property::TYPE)
                ])
            ->add('city')
            ->add('adress')
            ->add('postalcode')
            ->add('sold')
            ->add('images', CollectionType::class,
                array(
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'by_reference' => false,
                    'required' => false,
                    'entry_options' => ['label' => false]
                ));

        $builder->get('images')
                ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'translation_domain' => 'forms'
        ]);
    }
}
