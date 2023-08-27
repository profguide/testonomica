<?php

namespace App\Controller;

use App\Domain\Test\TestSearchForm;
use App\Entity\Result;
use App\Entity\Test;
use App\Repository\AuthorRepository;
use App\Service\CalculatorService;
use App\Service\ResultService;
use App\Service\TestService;
use App\Subscriber\Locale;
use App\Test\ResultRenderer;
use App\Test\TestStatus;
use App\Test\ViewFormat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    public function __construct(
        private readonly TestSearchForm    $testSearchForm,
        private readonly TestService       $testService,
        private readonly ResultService     $resultService,
        private readonly CalculatorService $calculatorService,
        private readonly ResultRenderer    $resultRenderer,
        private readonly AuthorRepository  $authors,
        private readonly Locale            $locale,
    )
    {
    }

    #[Route('/tests/', name: 'tests.index')]
    public function index(Request $request): Response
    {
        return $this->render('tests/index.html.twig', [
            'pagination' => $this->testSearchForm->search($request)
        ]);
    }

    #[Route('/tests/author/{slug}/', name: 'tests.author')]
    public function author(Request $request, string $slug): Response
    {
        $author = $this->authors->findOneBySlug($slug) ?? throw self::createNotFoundException();
        return $this->render('tests/author.html.twig', [
            'author' => $author,
            'pagination' => $this->testSearchForm->search($request, ['author' => $author])
        ]);
    }

    #[Route('/tests/view/{slug}/', name: 'tests.view')]
    public function view(Request $request, string $slug): Response
    {
        $test = $this->getTest($slug);
        return $this->render('tests/view.html.twig', [
            'test' => $test,
            'category' => $test->getCatalog(),
            'host' => self::host($request)
        ]);
    }

    #[Route('/tests/result/{uuid}/', name: 'tests.result')]
    public function result(string $uuid): Response
    {
        $result = $this->getResult($uuid);
        $test = $result->getTest();
        $data = array_merge([
            'test' => $test,
            'uuid' => $result->getUuid(),
            'status' => TestStatus::finished()
        ], $this->calculatorService->calculate($result));

        return $this->render('tests/result.html.twig', [
            'test' => $test,
            'status' => TestStatus::finished(),
            'uuid' => $result->getUuid(),
            'result' => $this->resultRenderer->render($test, $data, new ViewFormat(ViewFormat::HTML))->getContent()
        ]);
    }

    #[Route('/tests/iframe/', name: 'tests.iframe')]
    public function iframe(): Response
    {
        return $this->render('tests/iframe.html.twig');
    }

    private function getTest(string $slug): Test
    {
        $test = $this->testService->findBySlug($slug) ?? throw self::createNotFoundException();
        if (!$test->isActive($this->locale->getValue())) {
            throw self::createNotFoundException();
        }
        return $test;
    }

    private function getResult(string $uuid): Result
    {
        $result = $this->resultService->findByUuid($uuid);
        if (!$result) {
            throw self::createNotFoundException();
        }
        return $result;
    }

    private static function host(Request $request): string
    {
        return $request->getScheme() . '://' . $request->getHttpHost();
    }

//    private function assertAccess(Test $test, Request $request)
//    {
////        if ($this->getParameter('kernel.environment') === 'dev') {
////            return;
////        }
//        // Определим является ли тест платным
//        // Надо создать Service (пакет), ServiceTests (тесты в пакете) и Access (доступ к пакету (добавить поле service_id))
//        if ($test->getSlug() !== 'proforientation-v2') {
//            return;
//        }
//        // Из куки получать Access, находить Service по access.getService() и смотреть service.hasTest($test)
//        $token = $this->accessService->getCookie($request);
//        if ($token) {
//            $access = $this->accessService->findOneByToken($token);
//            if ($access) {
//                $access->setUsed();
//                $this->accessService->save($access);
//                return;
//            }
//        }
//        throw new AccessDeniedHttpException();
//    }
}