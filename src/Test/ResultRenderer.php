<?php

namespace App\Test;

use App\Entity\Test;
use App\Test\Analyzer\AnalysisRenderer;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ResultRenderer
{
    private Environment $twig;

    private Pdf $pdf;

    private AnalysisRenderer $analysisRenderer;

    public function __construct(Environment $twig, Pdf $pdf, AnalysisRenderer $analysisRenderer)
    {
        $this->twig = $twig;
        $this->pdf = $pdf;
        $this->analysisRenderer = $analysisRenderer;
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

    private function html(Test $test, array $data): string
    {
        // templated from the db
        $resultBlocksOutput = $this->analysisRenderer->render($test, $data);
        if (!empty($resultBlocksOutput) || $test->hasResultView()) {
            $template = $resultBlocksOutput . $test->getResultView();
            $template = $this->twig->createTemplate($template);
            return $template->render($data);
        } else {
            return $this->twig->render('tests/result/' . ResultUtil::resolveViewName($test) . '.html.twig', $data);
        }
    }
}