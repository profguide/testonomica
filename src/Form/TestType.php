<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Test;
use App\Service\CategoryService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
                'attr' => array('style' => 'width: 70px'),
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
            ->add('sourceName', null, [
                'attr' => [
                    'placeholder' => 'Например, proftest',
                ],
                'label' => 'Имя источника (вопросы, конфиг, калькулятор, шаблон)',
                'help' => 'Если пусто, то будет последовательная попытка найти ресурс по адресу URL (xml, view), затем по ID (xml, view, calculator), и затем Auto.',
            ])
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false,
                'required' => false,
                'choice_label' => 'name',
                'label' => 'Авторы'
            ])
            ->add('moderatorComment', TextareaType::class, [
                'required'   => false,
                'label' => 'Комментарии модератора',
            ])
//            ->add('authors', ChoiceType::class, [
//                'choices' => [
//                    'In Stock' => true,
//                    'Out of Stock' => false,
//                ],
//            ])
//            ->add('authors', CollectionType::class, [
//                'entry_type' => TestAuthorType::class,
//                'entry_options' => ['label' => false],
//                'allow_add' => true,
//                'allow_delete' => true,
////                'allow_add' => true,
////                'class' => Author::class,
////                'choice_label' => 'name',
////                'required' => true,
////                'expanded' => true,
////                'multiple' => true,
//            ])
//            ->add('authors', CollectionType::class, [
//                'entry_type' => TestAuthorType::class,
//                'allow_add' => true,
////                'class' => Author::class,
////                'choice_label' => 'name',
////                'required' => true,
////                'expanded' => true,
////                'multiple' => true,
//            ])
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
