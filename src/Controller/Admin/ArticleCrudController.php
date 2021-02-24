<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('index', 'Статьи');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('name', 'Название'),
            Field::new('nameEn', 'Название (en)'),
            Field::new('slug'),
            AssociationField::new('catalog'),
            TextEditorField::new('content', 'Контент')->hideOnIndex(),
            TextEditorField::new('contentEn', 'Контент (en)')->hideOnIndex(),
            Field::new('subtitle', 'Подзаголовок')->hideOnIndex(),
            Field::new('subtitleEn', 'Подзаголовок (en)')->hideOnIndex(),
            TextEditorField::new('annotation', 'Вступление')->hideOnIndex(),
            TextEditorField::new('annotationEn', 'Вступление (en)')->hideOnIndex(),
            TextField::new('metaTitle', 'Meta title')->hideOnIndex(),
            TextField::new('metaTitleEn', 'Meta title (en)')->hideOnIndex(),
            TextareaField::new('metaDescription', 'Meta description')->hideOnIndex(),
            TextareaField::new('metaDescriptionEn', 'Meta description (en)')->hideOnIndex(),
            TextField::new('imgFile', 'Изображение')
                ->setFormType(VichImageType::class)->hideOnIndex(),
            TextField::new('imgWideFile', 'Изображение')
                ->setFormType(VichImageType::class)->hideOnIndex(),
            AssociationField::new('test'),
            BooleanField::new('active', 'Активность'),
            BooleanField::new('activeEn', 'Активность (en)'),
        ];
    }
}
