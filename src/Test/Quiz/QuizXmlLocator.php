<?php

declare(strict_types=1);

namespace App\Test\Quiz;

use App\Entity\Test;
use App\Kernel;

final readonly class QuizXmlLocator
{
    public function __construct(private Kernel $kernel)
    {
    }

    public function resolveByTest(Test $test): string
    {
        // try new way first
        $location = $this->kernel->getProjectDir() . "/xml/{$test->getSlug()}/quiz.xml";
        if (file_exists($location)) {
            return $location;
        }

        $name = $test->getXmlFilename() ?? $test->getId();
        return $this->kernel->getProjectDir() . "/xml/$name.xml";
    }
}