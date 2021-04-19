<?php

namespace App\Form;

use App\Entity\Analysis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
                'row_attr' => [
                    'class' => 'col-12 optional-field',
                ]
            ])->add('text', TextareaType::class, [
                'label' => 'Описание (необязательно)',
                'row_attr' => [
                    'class' => 'col-12 optional-field',
                ]
            ])
            ->add(
                $builder->create('progressbar', FormType::class, [
                    'inherit_data' => true,
                    'label' => 'Прогресбар',
                    'attr' => [
                        'class' => 'form-row',
                        'style' => 'margin: 0;'
                    ],
                    'row_attr' => [
                        'class' => 'optional-field',
                        'style' => 'margin:0;padding:0'
                    ]
                ])
                    ->add('progressPercentVariableName', TextType::class, [
                        'label' => 'Процентная переменная прогресбара',
                        'row_attr' => [
                            'class' => 'col-4',
                        ]
                    ])
                    ->add('progressVariableName', TextType::class, [
                        'label' => 'Переменная значения прогрессбара',
                        'row_attr' => [
                            'class' => 'col-4',
                        ]
                    ])->add('progressVariableMax', TextType::class, [
                        'label' => 'Максимальное значение прогрессбара',
                        'row_attr' => [
                            'class' => 'col-4',
                        ]
                    ])
            )
            ->add('blocks', CollectionType::class, [
                'entry_type' => AnalysisBlockType::class,
                'entry_options' => [
                    'label' => false,
                    'row_attr' => [
                        'class' => 'card mb-3'
                    ],
                    'attr' => [
                        'class' => 'card-body',
                        'style' => 'padding: 0 15px'
                    ]
                ],
                'prototype' => true,
                'prototype_name' => '__analysis_block__',
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'Условные заключения',
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