<?php

declare(strict_types=1);

namespace App\Tests\Test;

use App\DataFixtures\TestFixture;
use App\Entity\Test;
use App\Repository\TestRepository;
use App\Test\ResultRenderer;
use App\Test\ViewFormat;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Проверям работу рендерера: HTML, PDF, JSON
 * @see ResultRenderer
 */
class ResultRendererTest extends KernelTestCase
{
    private ?ResultRenderer $renderer = null;
    private ?Test $test = null;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->renderer = self::getContainer()->get(ResultRenderer::class);

        /**@var $testRepo TestRepository */
        $testRepo = self::getContainer()->get(TestRepository::class);
        $this->test = $testRepo->findOneBySlug(TestFixture::TEST_1_SLUG);
    }

    public function testJson()
    {

        $data = ['score' => 96];
        $result = $this->renderer->render($this->test, $data, new ViewFormat(ViewFormat::JSON));
        self::assertEquals('{"score":96}', $result->getContent());
        self::assertEquals('application/json', $result->headers->get('Content-Type'));
    }

    public function testPdf()
    {

        $data = [];
        $result = $this->renderer->render($this->test, $data, new ViewFormat(ViewFormat::PDF));
        self::assertEquals('application/pdf', $result->headers->get('Content-Type'));
    }

    public function testHtml()
    {

        $data = [];
        $result = $this->renderer->render($this->test, $data, new ViewFormat(ViewFormat::HTML));
        self::assertStringContainsString('This is a test result', $result->getContent());
    }
}