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
        $tryName = $this->kernel->getProjectDir() . "/templates/tests/result/{$test->getSlug()}.html.twig";
        if (file_exists($tryName)) {
            return "tests/result/{$test->getSlug()}.html.twig";
        }
        // todo еще есть getXmlFilename - но от него надо избавляться.
        return "tests/result/{$test->getId()}.html.twig";
    }
}