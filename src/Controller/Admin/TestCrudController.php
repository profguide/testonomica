<?php

namespace App\Controller\Admin;

use App\Entity\Test;
use App\Form\QuestionType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class TestCrudController
 * @package App\Controller\Admin
 * @IsGranted("ROLE_ADMIN")
 */
class TestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Test::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('index', 'Тесты')
            ->setFormThemes([
                'admin/form.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig'
            ]);
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
//            ->addCssFile('admin')
            ->addWebpackEncoreEntry('admin');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel(null, 'fa fa-sliders-h'),
            TextField::new('name', 'Название'),
            Field::new('nameEn', 'Название (en)'),
            Field::new('slug'),
            AssociationField::new('catalog'),
            IntegerField::new('duration', 'Продолжительность в минутах')->hideOnIndex(),
            TextEditorField::new('annotation', 'Вступление')->hideOnIndex(),
            TextEditorField::new('annotationEn', 'Вступление (en)')->hideOnIndex(),
            TextEditorField::new('description', 'Описание на странице')->hideOnIndex(),
            TextEditorField::new('descriptionEn', 'Описание на странице (en)')->hideOnIndex(),
            BooleanField::new('active', 'Активность'),
            BooleanField::new('activeEn', 'Активность (en)'),
            BooleanField::new('inList', 'В списках'),
            BooleanField::new('isXmlSource', 'XML источник'),
            Field::new('xmlFilename', 'Xml name')->onlyOnForms(),
            Field::new('calculatorName', 'Calculator prefix name')->onlyOnForms(),
            FormField::addPanel('Вопросы', 'fa fa-question-circle')
                ->addCssClass('test-questions-form'), // @see admin.css
            CollectionField::new('questions', 'Вопросы')
                ->allowAdd(true)
                ->allowDelete(true)
                ->setEntryIsComplex(true)
                ->setEntryType(QuestionType::class)
                ->setFormTypeOptions([
                    'by_reference' => false,
                    'label' => false,
                    'prototype' => true,
                    'entry_options' => [
                        'label' => false,
                    ],
                ])
        ];
    }
}
