<?php

namespace App\Controller\Admin;

use App\Entity\Test;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class TestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Test::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('index', 'Тесты');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('name', 'Название'),
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
            Field::new('xmlFilename', 'Xml name')->onlyOnForms(),
            Field::new('calculatorName', 'Calculator prefix name')->onlyOnForms(),
        ];
    }
}
