<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Extractor\BoolParamExtractor;
use App\Repository\TestRepository;
use App\Service\TestSourceService;
use App\Test\TestDetailsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tests/api/v1', format: "json")]
class TestInfoRestController extends AbstractTestRestController
{
    public function __construct(
        private readonly TestDetailsService $detailsService,
        TestRepository                      $tests,
        TestSourceService                   $questions)
    {
        parent::__construct($tests, $questions);
    }

    #[Route("/info/{testId<[\w-]+>}/", methods: ["GET"])]
    public function info(string $testId, Request $request, BoolParamExtractor $boolParamExtractor): Response
    {
        $locale = $request->getLocale();
        $test = $this->getTest($testId);
        $withQuestions = $boolParamExtractor->extract($request, 'questions') ?? false;

        return $this->json($this->detailsService->get($test, $locale, $withQuestions));
    }
}