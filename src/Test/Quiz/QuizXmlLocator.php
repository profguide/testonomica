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
        // sourceName-based
        if ($test->getSourceName()) {
            $location = $this->kernel->getProjectDir() . "/xml/{$test->getSourceName()}/quiz.xml";
            if (file_exists($location)) {
                return $location;
            }
        }

        // alias-based
        $location = $this->kernel->getProjectDir() . "/xml/{$test->getSlug()}/quiz.xml";
        if (file_exists($location)) {
            return $location;
        }

        // deprecated way, it is non-flexible, because it makes to keep ids
        $name = $test->getXmlFilename() ?? $test->getId();
        return $this->kernel->getProjectDir() . "/xml/$name.xml";
    }
}