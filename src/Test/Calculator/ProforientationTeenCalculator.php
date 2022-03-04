<?php
/**
 * @author: adavydov
 * @since: 03.11.2020
 */

namespace App\Test\Calculator;

class ProforientationTeenCalculator extends AbstractProforientationCalculator
{
    protected function getProfessionsFileName(): string
    {
        return $this->kernel->getProjectDir() . "/xml/proftest/professions.xml";
    }
}