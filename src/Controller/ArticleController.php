<?php


namespace App\Controller;

use App\Domain\Article\ArticlesSearchForm;
use App\Entity\Article;
use App\Entity\ArticleCatalog;
use App\Repository\ArticleCatalogRepository;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/articles", name="articles.")
 * Class ArticleController
 * @package App\Controller
 */
class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository        $articleRepository,
        private readonly ArticlesSearchForm       $articlesSearchForm,
        private readonly ArticleCatalogRepository $articleCatalogRepository)
    {
    }

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function main(Request $request): Response
    {
        return $this->render('articles/main.html.twig', [
            'articles' => $this->articlesSearchForm->search($request),
            'catalogs' => $this->articleCatalogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/catalog/{slug}/", name="catalog")
     * @param string $slug
     * @return Response
     */
    public function catalog(Request $request, string $slug): Response
    {
        $catalog = $this->loadCatalog($slug);
        return $this->render('articles/catalog.html.twig', [
            'catalog' => $catalog,
            'articles' => $this->articlesSearchForm->search($request),
            'catalogs' => $this->articleCatalogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{slug}/", name="view")
     * @param string $slug
     * @return Response
     */
    public function view(string $slug): Response
    {
        return $this->render('articles/view.html.twig', [
            'article' => $this->loadArticle($slug),
            'catalogs' => $this->articleCatalogRepository->findAll(),
        ]);
    }

    private function loadArticle(string $slug): Article
    {
        return $this->articleRepository->findBySlug($slug) ?? throw self::createNotFoundException();
    }

    private function loadCatalog(string $slug): ArticleCatalog
    {
        return $this->articleCatalogRepository->findBySlug($slug) ?? throw self::createNotFoundException();
    }
}