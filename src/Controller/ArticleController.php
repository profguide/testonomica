<?php


namespace App\Controller;

use App\Domain\Article\ArticlesSearchForm;
use App\Entity\Article;
use App\Entity\ArticleCatalog;
use App\Repository\ArticleCatalogRepository;
use App\Repository\ArticleRepository;
use App\Subscriber\Locale;
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
        private readonly ArticleCatalogRepository $articleCatalogRepository,
        private readonly Locale                   $locale)
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
            'allowed_locales' => ['ru', 'en']
        ]);
    }

    /**
     * @Route("/catalog/{slug}/", name="catalog")
     * @param Request $request
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
            'allowed_locales' => ['ru', 'en']
        ]);
    }

    /**
     * @Route("/{slug}/", name="view")
     * @param string $slug
     * @return Response
     */
    public function view(string $slug): Response
    {
        $article = $this->loadArticle($slug);
        return $this->render('articles/view.html.twig', [
            'article' => $article,
            'catalogs' => $this->articleCatalogRepository->findAll(),
            'allowed_locales' => [
                $article->isActive('ru') ? 'ru' : null,
                $article->isActive('en') ? 'en' : null,
            ]
        ]);
    }

    private function loadArticle(string $slug): Article
    {
        $article = $this->articleRepository->findBySlug($slug) ?? throw self::createNotFoundException();
        if (!$article->isActive($this->locale->getValue())) {
            throw self::createNotFoundException();
        }
        return $article;
    }

    private function loadCatalog(string $slug): ArticleCatalog
    {
        return $this->articleCatalogRepository->findBySlug($slug) ?? throw self::createNotFoundException();
    }
}