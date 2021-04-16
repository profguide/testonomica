<?php

namespace App\Form;

use App\Entity\Analysis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnalysisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Заголовок (необязательно)',
                'required' => true
            ])->add('progressPercentVariableName', TextType::class, [
                'label' => 'Заполнение прогрессбара',
                'required' => true
            ])->add('progressVariableName', TextType::class, [
                'label' => 'Чистое значение',
                'required' => true
            ])->add('progressVariableMax', TextType::class, [
                'label' => 'Максимальное значение',
                'required' => true
            ])
            ->add('blocks', CollectionType::class, [
                'entry_type' => AnalysisBlockType::class,
                'entry_options' => [
                    'label' => false,
                    'row_attr' => [
                        'class' => 'card mb-3'
                    ],
                    'attr' => [
                        'class' => 'card-body'
                    ]
                ],
                'prototype' => true,
                'prototype_name' => '__analysis_block__',
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'Варианты',
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Analysis::class,
        ]);
    }
}