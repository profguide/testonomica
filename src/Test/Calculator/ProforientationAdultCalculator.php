<?php
/**
 * @author: adavydov
 * @since: 03.11.2020
 */

namespace App\Test\Calculator;

class ProforientationAdultCalculator extends AbstractProforientationCalculator
{
    protected function getProfessionsFileName(): string
    {
        return $this->kernel->getProjectDir() . "/xml/proforientationTeenProfessions.xml";
    }
}