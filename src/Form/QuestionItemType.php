<?php

namespace App\Form;

use App\Entity\QuestionItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextType::class, [
                'label' => 'Текст',
                'required' => true
            ])
//            ->add('textEn', TextType::class, [
//                'label' => 'Текст (EN)'
//            ])
            ->add('value', TextType::class, [
                'label' => 'Истинное значение (слитно)',
                'required' => true
            ])
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