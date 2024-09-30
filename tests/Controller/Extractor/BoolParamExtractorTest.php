<?php

declare(strict_types=1);

namespace App\Tests\Controller\Extractor;

use App\Controller\Extractor\BoolParamExtractor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

final class BoolParamExtractorTest extends KernelTestCase
{
    public function testTrue()
    {
        $request = new Request(['enabled' => 'true']);

        $extractor = new BoolParamExtractor();
        $value = $extractor->extract($request, 'enabled');

        $this->assertSame(true, $value);
    }

    public function testFalse()
    {
        $request = new Request(['enabled' => 'false']);

        $extractor = new BoolParamExtractor();
        $value = $extractor->extract($request, 'enabled');

        $this->assertSame(false, $value);
    }

    public function testWrongThrowsException()
    {
        $request = new Request(['enabled' => '0']);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Invalid value for "enabled". Only "true" or "false" is expected.');
        $this->expectExceptionCode(400);

        $extractor = new BoolParamExtractor();
        $extractor->extract($request, 'enabled');
    }

    public function testRequiredThrowsException()
    {
        $request = new Request();

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Missing "enabled" parameter.');
        $this->expectExceptionCode(400);

        $extractor = new BoolParamExtractor();
        $extractor->extract($request, 'enabled', true);
    }
}