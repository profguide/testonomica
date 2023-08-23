<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Article;
use App\Repository\ArticleCatalogRepository;
use App\Repository\TestRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ArticleType extends AbstractType
{
    public function __construct(private readonly ArticleCatalogRepository $catalogs, private readonly TestRepository $tests)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('nameEn')
            ->add('subtitle')
            ->add('subtitleEn')
            ->add('content')
            ->add('contentEn')
            ->add('annotation', TextareaType::class)
            ->add('annotationEn', TextareaType::class)
            ->add('metaTitle')
            ->add('metaTitleEn')
            ->add('metaDescription')
            ->add('metaDescriptionEn')
            ->add('slug', null, [
                'label' => 'Адрес URL',
                'attr' => array('style' => 'width: 300px'),
            ])
            ->add('catalog', ChoiceType::class, [
                'label' => 'Каталог',
                'choices' => $this->catalogs->findAll(),
                'choice_value' => 'id',
                'choice_label' => 'name',
                'attr' => array('style' => 'width: 300px'),
            ])
            ->add('test', ChoiceType::class, [
                'label' => 'Тест',
                'choices' => $this->tests->findAll(),
                'choice_value' => 'id',
                'choice_label' => 'name',
                'attr' => array('style' => 'width: 300px'),
            ])
            ->add('active', ChoiceType::class, [
                'label' => 'Доступность RU',
                'choices' => [
                    'Выключено' => 0,
                    'Включено' => 1,
                    'Включено по ссылке' => 2,
                ],
            ])
            ->add('activeEn', ChoiceType::class, [
                'label' => 'Доступность EN',
                'choices' => [
                    'Выключено' => 0,
                    'Включено' => 1,
                    'Включено по ссылке' => 2,
                ],
            ])
            ->add('imgWideFile', VichImageType::class, [
                'required' => false,
                'download_label' => false,
                'row_attr' => [
                    'class' => 'vich-uploader-wrapper'
                ],
                'label' => 'Широкая картинка в статье'
            ])
            ->add('imgFile', VichImageType::class, [
                'required' => false,
                'download_label' => false,
                'row_attr' => [
                    'class' => 'vich-uploader-wrapper'
                ],
                'label' => 'Маленькая картинка для списка'
            ])
            ->add('save', SubmitType::class, [
                'row_attr' => ['style' => 'padding-top: 15px']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}