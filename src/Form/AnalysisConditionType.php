<?php


namespace App\Form;


use App\Entity\AnalysisCondition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnalysisConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('variableName', TextType::class, [
                'label' => 'Переменная',
            ])->add('comparison', ChoiceType::class, [
                'label' => 'Сравнение',
                'choices' => [
                    'равно' => '==',
                    'больше' => '>',
                    'меньше' => '<',
                    'больше или равно' => '>=',
                    'меньше или равно' => '<='
                ]
            ])->add('referentValue', TextType::class, [
                'label' => 'Значение',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AnalysisCondition::class,
        ]);
    }
}