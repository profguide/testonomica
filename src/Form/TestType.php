<?php

namespace App\Form;

use App\Entity\Test;
use App\Service\CategoryService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestType extends AbstractType
{
    public function __construct(private readonly CategoryService $categoryService)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('nameEn')
            ->add('slug', null, [
                'label' => 'Адрес URL',
                'attr' => array('style' => 'width: 300px'),
                'help' => 'Этот адрес URL также используется как идентификатор директории с xml теста'
            ])
            ->add('catalog', ChoiceType::class, [
                'choices' => $this->categoryService->findAll(),
                'choice_value' => 'id',
                'choice_label' => 'name',
            ])
            ->add('description')
            ->add('descriptionEn')
            ->add('annotation')
            ->add('annotationEn')
            ->add('duration', IntegerType::class, [
                'attr' => array('style' => 'width: 50px'),
                'label' => 'Продолжительность в минутах'
            ])
            ->add('active', null, [
                'label' => 'Доступен RU'
            ])
            ->add('activeEn', null, [
                'label' => 'Доступен EN'
            ])
            ->add('inList', null, [
                'label' => 'Виден в списке'
            ])
            // todo check if it exist - just let them know
            ->add('calculatorName', null, [
                'label' => 'Калькулятор (имя файла)',
                'help' => 'Например, "Proftest" будет интерпретировано как ProftestCalculator. Если пусто, то будет использован автоматический калькулятор.'
            ])
            ->add('save', SubmitType::class, [
                'row_attr' => ['style' => 'padding-top: 15px']]);
        //            ->add('catalog')
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Test::class,
        ]);
    }
}
