<?php
/**
 * @author: adavydov
 * @since: 03.11.2020
 */

namespace App\Test\Calculator;

use Symfony\Component\HttpKernel\KernelInterface;

class ProforientationAdultCalculator extends ProforientationAbstractCalculator
{
    /**@var KernelInterface */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        parent::__construct();
    }

    protected function getTestSourceFileName(): string
    {
        return $this->kernel->getProjectDir() . "/xml/proforientationAdult.xml";
    }

    protected function getProfessionsFileName(): string
    {
        return $this->kernel->getProjectDir() . "/xml/proforientationTeenProfessions.xml";
    }
}