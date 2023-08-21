<?php

declare(strict_types=1);

namespace App\Test\Config;

use App\Entity\Test;
use App\Kernel;

final readonly class ConfigXmlLocator
{
    public function __construct(private Kernel $kernel)
    {
    }

    public function resolveByTest(Test $test): string
    {
        if ($test->getSourceName()) {
            $location = $this->kernel->getProjectDir() . "/xml/{$test->getSourceName()}/config.xml";
            if (file_exists($location)) {
                return $location;
            }
        }
        return $this->kernel->getProjectDir() . "/xml/{$test->getSlug()}/config.xml";
    }
}