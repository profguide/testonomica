<?php

namespace App\Admin\Type;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('id', HiddenType::class)
//            ->add('test', EntityType::class, ['class' => Test::class])
            ->add('name', TextType::class, [
//                'columns' => 4,
//                'by_reference' => false,
                'required' => true
            ])
//            ->add('name')
            ->add('name_en')
            ->add('text')
            ->add('text_en')
            ->add('variety')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Option' => Question::TYPE_OPTION,
                    'Text' => Question::TYPE_TEXT,
                    'Rating' => Question::TYPE_RATING,
                    'Checkbox' => Question::TYPE_CHECKBOX,
                ]
            ])
            ->add('wrong')
            ->add('wrong_en')
            ->add('correct')
            ->add('correct_en')
            ->add('enabled_back', CheckboxType::class)
            ->add('enabled_forward', CheckboxType::class)
            ->add('show_answer', CheckboxType::class)
            ->add('items', CollectionType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}