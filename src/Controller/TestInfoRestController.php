<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/tests/api/v1", format="json")
 */
class TestInfoRestController extends TestRestController
{
    /**
     * @Route("/info/{testId<\d+>}/")
     * @param int $testId
     * @return Response
     */
    public function info(int $testId): Response
    {
        $test = $this->getTest($testId);
        $length = $this->questions->getTotalCount($test);
        return $this->json([
            'name' => $test->getName(),
            'description' => $test->getDescription(),
            'duration' => $test->getDuration(),
            'length' => $length,
            'paid' => !$test->isFree()
        ]);
    }
}