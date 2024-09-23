<?php

namespace App\Test;

use App\Entity\Test;
use App\Test\Analyzer\AnalysisRenderer;
use App\Test\Config\ConfigParser;
use App\Test\Config\ConfigXmlFetcher;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ResultRenderer
{
    public function __construct(
        private Environment          $twig,
        private Pdf                  $pdf,
        private AnalysisRenderer     $analysisRenderer,
        private ConfigParser         $configParser,
        private ConfigXmlFetcher     $configXmlFetcher,
        private TestViewNameResolver $viewNameResolver)
    {
    }

    /**
     * Рендерит HTML результата
     * @param Test $test
     * @param array $data
     * @param ViewFormat $format
     * @return Response
     */
    public function render(Test $test, array $data, ViewFormat $format): Response
    {
        switch ($format->value()) {
            case ViewFormat::HTML:
                return new Response($this->html($test, $data));
            case ViewFormat::JSON:
                return $this->json($data);
            case ViewFormat::PDF:
                return $this->pdf($test, $data);
        }
        throw new \RuntimeException("Unsupported render format: {$format->value()}.");
    }

    private function json(array $data): JsonResponse
    {
        $response = new JsonResponse($data);
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        return $response;
    }

    /**
     * Renders PDF based of HTML
     *
     * @param Test $test
     * @param array $data
     * @return Response
     */
    private function pdf(Test $test, array $data): Response
    {
        // other useful params:
        // https://stackoverflow.com/questions/30303218/bad-characters-when-generating-pdf-file-with-knp-snappy
        // todo wrap with empty html
        $pdf = $this->pdf->getOutputFromHtml($this->html($test, $data), [
            'encoding' => 'utf-8',
        ]);
        $name = 'report_' . date('d.m.Y_h.i') . '.pdf';
        return new PdfResponse($pdf, $name);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    private function html(Test $test, array $data): string
    {
        $resultBlocksOutput = $this->analysisRenderer->render($test, $data);

        $configVariables = $this->getConfigVariables($test);
        if (!empty($configVariables)) {
            $data['config'] = $configVariables;
        }

        if (!empty($resultBlocksOutput) || $test->hasResultView()) {
            $template = $resultBlocksOutput . $test->getResultView();
            $template = $this->twig->createTemplate($template);
            return $template->render($data);
        } else {
            return $this->twig->render($this->viewNameResolver->resolveByTest($test), $data);
        }
    }

    private function getConfigVariables(Test $test): array
    {
        if (!$this->configXmlFetcher->exist($test)) {
            return [];
        }

        $crawler = $this->configXmlFetcher->fetchByTest($test);
        $config = $this->configParser->parse($crawler);

        return $config->getAllVariables();
    }
}