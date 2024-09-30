<?php

declare(strict_types=1);

namespace App\Test;

use App\Entity\Test;
use App\Service\TestSourceService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Test details.
 * Can be used for controller response.
 * @see TestDetailsServiceTest
 */
final readonly class TestDetailsService
{
    public function __construct(private TestSourceService $questions, private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function get(Test $test, string $locale, bool $withQuestions): array
    {
        $questions = $withQuestions ? $this->getQuestions($test) : [];
        $length = $withQuestions ? count($questions) : $this->getQuestionCount($test);
        $authors = $this->getAuthorsInfo($test, $locale);

        return [
            'name' => $test->getName($locale),
            'description' => $test->getDescription($locale),
            'instruction' => $this->getInstruction($test),
            'duration' => $test->getDuration(),
            'length' => $length,
            'paid' => !$test->isFree(),
            'questions' => $questions,
            'authors' => $authors,
        ];
    }

    private function getQuestions(Test $test): array
    {
        return $this->questions->getAll($test);
    }

    private function getQuestionCount(Test $test): int
    {
        return $this->questions->getTotalCount($test);
    }

    private function getInstruction(Test $test): ?string
    {
        return $this->questions->getInstruction($test);
    }

    private function getAuthorsInfo(Test $test, string $locale): array
    {
        $authors = [];
        foreach ($test->getAuthors() as $author) {
            $authors[] = [
                'name' => $author->getName($locale),
                'url' => $this->generateUrlForAuthor($author),
            ];
        }

        return $authors;
    }

    private function generateUrlForAuthor($author): string
    {
        return $this->urlGenerator->generate('tests.author', ['slug' => $author->getSlug()]);
    }
}