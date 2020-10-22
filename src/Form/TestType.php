<?php

namespace App\Form;

use App\Entity\Test;
use App\Service\CategoryService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestType extends AbstractType
{
    private $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('catalog', ChoiceType::class, [
                'choices' => $this->categoryService->findAll(),
                'choice_value' => 'id',
                'choice_label' => 'name',
            ])
            ->add('slug')
            ->add('name')
            ->add('nameEn')
            ->add('description')
            ->add('descriptionEn')
            ->add('annotation')
            ->add('annotationEn')
            ->add('duration')
            ->add('active')
            ->add('activeEn')
            ->add('xmlFilename')
            ->add('save', SubmitType::class, [
                'row_attr' => ['style' => 'padding-top: 15px']]);
        //            ->add('catalog')
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Test::class,
        ]);
    }
}
