<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tests/api/v1", format="json")
 */
class TestInfoRestController extends AbstractTestRestController
{
    /**
     * @Route("/info/{testId<\d+>}/")
     * @param int $testId
     * @param Request $request
     * @return Response
     */
    public function info(int $testId, Request $request): Response
    {
        $locale = $request->getLocale();
        $test = $this->getTest($testId);
        $length = $this->questions->getTotalCount($test);
        return $this->json([
            'name' => $test->getName($locale),
            'description' => $test->getDescription($locale),
            'duration' => $test->getDuration(),
            'length' => $length,
            'paid' => !$test->isFree()
        ]);
    }
}