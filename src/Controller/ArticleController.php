<?php


namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleCatalog;
use App\Repository\ArticleCatalogRepository;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/articles", name="articles.")
 * Class ArticleController
 * @package App\Controller
 */
class ArticleController extends AbstractController
{
    private ArticleRepository $articleRepository;

    private ArticleCatalogRepository $articleCatalogRepository;


    public function __construct(ArticleRepository $articleRepository, ArticleCatalogRepository $articleCatalogRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->articleCatalogRepository = $articleCatalogRepository;
    }

    /**
     * @Route("/", name="index")
     * todo подгрузка, т.е. пейджинг
     * @return Response
     */
    public function main(): Response
    {
        return $this->render('articles/main.html.twig', [
            'articles' => $this->articleRepository->findAll(),
            'catalogs' => $this->articleCatalogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{slug}/", name="catalog")
     * @param string $slug
     * @return Response
     */
    public function catalog(string $slug): Response
    {
        $catalog = $this->loadCatalog($slug);
        return $this->render('articles/catalog.html.twig', [
            'catalog' => $catalog,
            'articles' => $this->loadArticlesByCatalog($catalog),
            'catalogs' => $this->articleCatalogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{catalogSlug}/{slug}/", name="view")
     * @param string $catalogSlug
     * @param string $slug
     * @return Response
     */
    public function view(string $catalogSlug, string $slug): Response
    {
        return $this->render('articles/view.html.twig', [
            'article' => $this->loadArticle($catalogSlug, $slug),
            'catalogs' => $this->articleCatalogRepository->findAll(),
        ]);
    }

    private function loadArticle(string $catalog, string $slug): Article
    {
        $article = $this->articleRepository->findBySlug($slug);
        if ($article == null) {
            throw self::createNotFoundException();
        }
        if ($article->getCatalog()->getSlug() != $catalog) {
            throw self::createNotFoundException();
        }
        return $article;
    }

    private function loadCatalog(string $slug): ArticleCatalog
    {
        $catalog = $this->articleCatalogRepository->findBySlug($slug);
        if ($catalog == null) {
            throw self::createNotFoundException();
        }
        return $catalog;
    }

    private function loadArticlesByCatalog(ArticleCatalog $catalog)
    {
        return $this->articleRepository->findBy(['catalog' => $catalog]);
    }
}