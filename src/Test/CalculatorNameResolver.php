<?php

declare(strict_types=1);

namespace App\Test;

use App\Entity\Test;
use App\Kernel;

final class CalculatorNameResolver
{
    const CALCULATORS_NAMESPACE = '\App\Test\Calculator\\';

    public function __construct(private Kernel $kernel)
    {
    }

    public function resolveByTest(Test $test): string
    {
        if ($test->getSourceName()) {
            $name = ucfirst($test->getSourceName());
            $location = $this->kernel->getProjectDir() . "/src/Test/Calculator/{$name}Calculator.php";
            if (file_exists($location)) {
                return self::CALCULATORS_NAMESPACE . $name . 'Calculator';
            }
        }

        // deprecated way: use Test::sourceName to get flexibility
        $location = $this->kernel->getProjectDir() . "/src/Test/Calculator/Test{$test->getId()}Calculator.php";
        if (file_exists($location)) {
            return self::CALCULATORS_NAMESPACE . 'Test' . $test->getId() . 'Calculator';
        }

        return self::CALCULATORS_NAMESPACE . 'AutoCalculator';
    }
}