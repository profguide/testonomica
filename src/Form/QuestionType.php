<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder->create('main', FormType::class, [
                    'inherit_data' => true,
                    'label' => false,
                    'attr' => [
                        'class' => 'form-row'
                    ]
                ])
                    ->add('name', TextType::class, [
                        'label' => 'Текст вопроса',
                        'required' => true,
                        'row_attr' => [
                            'class' => 'col-4',
                        ]
                    ])
                    ->add('type', ChoiceType::class, [
                        'label' => 'Тип вопроса',
                        'row_attr' => [
                            'class' => 'col-4',
                        ],
                        'choices' => [
                            'Один вариант' => Question::TYPE_OPTION,
                            'Несколько вариантов' => Question::TYPE_CHECKBOX,
                            'Ввод значения' => Question::TYPE_TEXT,
                            'Рейтинг' => Question::TYPE_RATING,
                        ]
                    ])
                    ->add('variety', TextType::class, [
                        'label' => 'Группа (слитно, необязательно)',
                        'row_attr' => [
                            'class' => 'col-4',
                        ]
                    ])
            )
//            ->add('name_en', TextType::class, [
//                'label' => 'Текст вопроса (EN)',
//            ])
            ->add('text', TextareaType::class, [
                'label' => 'Дополнительный текст (необязательно)',
                'row_attr' => [
                    'class' => 'full-width'
                ]
            ])
//            ->add('text_en', TextareaType::class, [
//                'label' => 'Дополнительный текст (EN)',
//            ])
            ->add('enabled_back', CheckboxType::class, [
                'label' => 'Кнопка назад доступна',
            ])
            ->add('enabled_forward', CheckboxType::class, [
                'label' => 'Кнопка пропустить доступна',
            ])
            ->add(
                $builder->create('correct_block', FormType::class, [
                    'inherit_data' => true,
                    'label' => false,
                    'row_attr' => [
                        'class' => 'border p-3'
                    ]
                ])->add('show_answer', CheckboxType::class, [
                    'label' => 'Моментальный показ правильного ответа',
                ])->add('correct', TextType::class, [
                    'label' => 'Правильный ответ (если включен показ)',
                ])->add('wrong', TextType::class, [
                    'label' => 'Неправильный ответ (если включен показ)',
                ])
//                ->add('correct_en', TextType::class, [
//                    'label' => 'Правильный ответ (EN) (если включен показ)',
//                ])
//                    ->add('wrong_en', TextType::class, [
//                        'label' => 'Неправильный ответ (EN) (если включен показ)',
//                    ])
            )
            ->add('items', CollectionType::class, [
                'entry_type' => QuestionItemType::class,
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
                'prototype_name' => '__item__',
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'Варианты ответов',
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
//            'prototype' => true,
        ]);
    }
}