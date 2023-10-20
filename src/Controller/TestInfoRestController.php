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
     * @Route("/info/{testId<[\w-]+>}/")
     * @param string $testId
     * @param Request $request
     * @return Response
     */
    public function info(string $testId, Request $request): Response
    {
        $locale = $request->getLocale();
        $test = $this->getTest($testId);
        $length = $this->questions->getTotalCount($test);
        $instruction = $this->questions->getInstruction($test);

        $authors = [];
        foreach ($test->getAuthors() as $author) {
            $authors[] = ['name' => $author->getName($locale), 'url' => $this->generateUrl('tests.author', ['slug' => $author->getSlug()])];
        }
        return $this->json([
            'name' => $test->getName($locale),
            'description' => $test->getDescription($locale),
            'instruction' => $instruction,
            'authors' => $authors,
            'duration' => $test->getDuration(),
            'length' => $length,
            'paid' => !$test->isFree()
        ]);
    }
}