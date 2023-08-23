<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Article;
use App\Repository\ArticleCatalogRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ArticleType extends AbstractType
{
    public function __construct(private readonly ArticleCatalogRepository $catalogs)
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
                'choices' => $this->catalogs->findAll(),
                'choice_value' => 'id',
                'choice_label' => 'name',
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
            ->add('imgWide', null, [
                'label' => 'Широкая картинка в статье'
            ])
            ->add('img', null, [
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