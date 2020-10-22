<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Test;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// @IsGranted("ROLE_ADMIN")
/**
 * @Route("/admin", name="admin.")
 */
class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/", name="index")
     * @return Response
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Менеджер');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Главная', 'fa fa-home');
        yield MenuItem::linkToCrud('Категории', 'fa fa-folder', Category::class);
        yield MenuItem::linkToCrud('Тесты', 'fa fa-tags', Test::class);
    }
}
