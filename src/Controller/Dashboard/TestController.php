<?php


namespace App\Controller\Dashboard;


use App\Repository\TestRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TestController
 * @Route("dashboard/test", name="dashboard.test.")
 * @package App\Controller\Dashboard
 */
class TestController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(TestRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        return $this->render('dashboard/test/index.html.twig', [
            'paginator' => $paginator->paginate(
                $repository->getWithSearchQueryBuilder($request->getQueryString()),
                $request->query->getInt('page', 1),
                10
            )
        ]);
    }
}