<?php

namespace App\Controller\Admin;

use App\Entity\ArticleCatalog;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleCatalogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ArticleCatalog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('index', 'Каталог статей');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('name', 'Название'),
            Field::new('nameEn', 'Название (en)'),
            Field::new('slug'),
            TextField::new('metaTitle', 'Meta title')->hideOnIndex(),
            TextField::new('metaTitleEn', 'Meta title (en)')->hideOnIndex(),
            TextareaField::new('metaDescription', 'Meta description')->hideOnIndex(),
            TextareaField::new('metaDescriptionEn', 'Meta description (en)')->hideOnIndex(),
        ];
    }
}
