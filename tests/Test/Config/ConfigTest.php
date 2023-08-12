<?php

declare(strict_types=1);

namespace App\Tests\Test\Config;

use App\Test\Config\Config;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ConfigTest extends KernelTestCase
{
    public function testChain()
    {
        $data = [
            'min' => [
                'text' => 'Текст'
            ]
        ];
        $config = new Config($data);

        self::assertEquals('Текст', $config->get('%config.min.text%'));
    }

    public function testErrorNotFound()
    {

    }
}