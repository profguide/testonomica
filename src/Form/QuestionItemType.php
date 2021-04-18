<?php

namespace App\Form;

use App\Entity\QuestionItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder->create('main', FormType::class, [
                    'inherit_data' => true,
                    'label' => false,
                    'attr' => [
                        'class' => 'form-row',
                    ],
                    'row_attr' => [
                        'style' => 'padding-bottom: 0'
                    ]
                ])
                    ->add('text', TextType::class, [
                        'label' => 'Текст',
                        'required' => true,
                        'help' => 'Пример: Москва',
                        'row_attr' => [
                            'class' => 'col-4',
                            'style' => 'padding-bottom: 0'
                        ]
                    ])
//            ->add('textEn', TextType::class, [
//                'label' => 'Текст (EN)'
//            ])
                    ->add('value', TextType::class, [
                        'label' => 'Значение (слитно)',
                        'required' => true,
                        'help' => 'Примеры: 1, moscow',
                        'row_attr' => [
                            'class' => 'col-4',
                            'style' => 'padding-bottom: 0'
                        ]
                    ])
            )
            ->add('correct', CheckboxType::class, [
                'label' => 'Является правильным ответом',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QuestionItem::class,
            'prototype' => true,
        ]);
    }
}