<?php
/**
 * @author: adavydov
 * @since: 03.11.2020
 */

namespace App\Test\Calculator;

class ProftestCalculator extends AbstractProforientationCalculator
{
    protected function professionsFileName(): string
    {
        return '/xml/proftest/professions.xml';
    }

    protected function configFileName(): string
    {
        return '/xml/proftest/config.xml';
    }
}