<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Текст вопроса',
                'required' => true
            ])
//            ->add('name_en', TextType::class, [
//                'label' => 'Текст вопроса (EN)',
//            ])
            ->add('text', TextareaType::class, [
                'label' => 'Дополнительный текст',
            ])
//            ->add('text_en', TextareaType::class, [
//                'label' => 'Дополнительный текст (EN)',
//            ])
            ->add('variety', TextType::class, [
                'label' => 'Групповой признак (слитно)',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Тип вопроса',
                'choices' => [
                    'Один вариант' => Question::TYPE_OPTION,
                    'Несколько вариантов' => Question::TYPE_CHECKBOX,
                    'Ввод значения' => Question::TYPE_TEXT,
                    'Рейтинг' => Question::TYPE_RATING,
                ]
            ])
            ->add('correct', TextType::class, [
                'label' => 'Правильный ответ (если включен показ)',
            ])
//            ->add('correct_en', TextType::class, [
//                'label' => 'Правильный ответ (EN) (если включен показ)',
//            ])
            ->add('wrong', TextType::class, [
                'label' => 'Неправильный ответ (если включен показ)',
            ])
//            ->add('wrong_en', TextType::class, [
//                'label' => 'Неправильный ответ (EN) (если включен показ)',
//            ])
            ->add('enabled_back', CheckboxType::class, [
                'label' => 'Кнопка назад',
            ])
            ->add('enabled_forward', CheckboxType::class, [
                'label' => 'Кнопка пропустить',
            ])
            ->add('show_answer', CheckboxType::class, [
                'label' => 'Показать правильный ответ',
            ])
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