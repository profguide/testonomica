<?php

declare(strict_types=1);

namespace App\Test;

use App\Entity\Test;
use App\Kernel;

final readonly class TestViewNameResolver
{
    public function __construct(private Kernel $kernel)
    {
    }

    public function resolveByTest(Test $test): string
    {
        // sourceName-based
        if ($test->getSourceName()) {
            $location = $this->kernel->getProjectDir() . "/templates/tests/result/{$test->getSourceName()}.html.twig";
            if (file_exists($location)) {
                return "tests/result/{$test->getSourceName()}.html.twig";
            }
        }

        $tryName = $this->kernel->getProjectDir() . "/templates/tests/result/{$test->getSlug()}.html.twig";
        if (file_exists($tryName)) {
            return "tests/result/{$test->getSlug()}.html.twig";
        }

        // deprecated way, it is non-flexible, because it makes to keep ids
        return "tests/result/{$test->getId()}.html.twig";
    }
}