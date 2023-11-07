<?php
/**
 * @author: adavydov
 * @since: 03.11.2020
 */

namespace App\Test\Calculator;

class ProftestDemoCalculator extends AbstractProforientationCalculator
{
    protected function professionsFileName(): string
    {
        return '/xml/proftestDemo/professions.xml';
    }

    protected function configFileName(): string
    {
        return '/xml/proftestDemo/config.xml';
    }
}