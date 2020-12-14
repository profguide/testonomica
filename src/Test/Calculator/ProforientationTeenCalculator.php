<?php
/**
 * @author: adavydov
 * @since: 03.11.2020
 */

namespace App\Test\Calculator;

use Symfony\Component\HttpKernel\KernelInterface;

class ProforientationTeenCalculator extends AbstractProforientationCalculator
{
    /**@var KernelInterface */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    protected function getProfessionsFileName(): string
    {
        return $this->kernel->getProjectDir() . "/xml/proforientationTeenProfessions.xml";
    }
}