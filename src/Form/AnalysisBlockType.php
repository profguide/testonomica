<?php

namespace App\Form;

use App\Entity\AnalysisBlock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnalysisBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextareaType::class, [
                'label' => 'Текст',
            ])
            ->add('conditions', CollectionType::class, [
                'entry_type' => AnalysisConditionType::class,
                'entry_options' => [
                    'label' => false,
                    'row_attr' => [
                        'class' => 'mb-3 border',
                        'style' => 'padding: 0 15px'
                    ],
                    'attr' => [
                        'class' => 'form-row',
                        'style' => 'margin: 0;'
                    ]
                ],
                'prototype' => true,
                'prototype_name' => '__analysis_condition__',
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'Условия',
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AnalysisBlock::class,
        ]);
    }
}