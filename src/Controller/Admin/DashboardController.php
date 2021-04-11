<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\ArticleCatalog;
use App\Entity\Category;
use App\Entity\Test;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// @IsGranted("ROLE_ADMIN")
/**
 * @Route("/admin")
 */
class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/")
     * @return Response
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Админка');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Главная', 'fa fa-home');
        yield MenuItem::section('Тесты');
        yield MenuItem::linkToCrud('Категории', 'fa fa-folder', Category::class);
        yield MenuItem::linkToCrud('Тесты', 'fa fa-brain', Test::class);
        yield MenuItem::section('Статьи');
        yield MenuItem::linkToCrud('Каталог статей', 'fa fa-folder', ArticleCatalog::class);
        yield MenuItem::linkToCrud('Статьи', 'fa fa-book-reader', Article::class);
    }
}
