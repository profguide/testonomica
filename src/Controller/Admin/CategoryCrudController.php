<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('index', 'Категории');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('name', 'Название'),
            Field::new('nameEn', 'Название (en)'),
            Field::new('slug'),
            ImageField::new('picFile', 'Изображение')
                ->setFormType(VichImageType::class)->hideOnIndex(),
            BooleanField::new('active', 'Активность'),
        ];
    }
}
