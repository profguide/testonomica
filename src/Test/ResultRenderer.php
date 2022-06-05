<?php

namespace App\Test;

use App\Entity\Test;
use App\Service\AnalysisRenderer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ResultRenderer
{
    private Environment $twig;

    private AnalysisRenderer $analysisRenderer;

    public function __construct(Environment $twig, AnalysisRenderer $analysisRenderer)
    {
        $this->twig = $twig;
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
        if ($format->value() === ViewFormat::HTML) {
            return $this->html($test, $data);
        } elseif ($format->value() === ViewFormat::JSON) {
            return $this->json($test, $data);
        } elseif ($format->value() === ViewFormat::PDF) {
            return $this->pdf($test, $data);
        }

        throw new \RuntimeException("Unsupported render format: {$format->value()}.");
    }

    private function json(Test $test, array $data): JsonResponse
    {
        if ($test->getSlug() === 'proforientation-v2') {
            $response = new JsonResponse($data);
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
            return $response;
        }
        throw new \DomainException('The test doest not support JSON report.');
    }

    private function pdf(Test $test, array $data): JsonResponse
    {
        if ($test->getSlug() === 'proforientation-v2') {
            return new BinaryFileResponse();
        }
        throw new \DomainException('The test doest not support PDF report.');
    }

    private function html(Test $test, array $data): Response
    {
        // templated from the db
        $resultBlocksOutput = $this->analysisRenderer->render($test, $data);
        if (!empty($resultBlocksOutput) || $test->getResultView() != null) {
//            $template = "{% extends('tests/result.html.twig') %}{% block result %}<div class=\"container\">"
//                . $resultBlocksOutput
//                . $test->getResultView()
//                . "</div>{% endblock %}";
            $template = $resultBlocksOutput . $test->getResultView();
            $template = $this->twig->createTemplate($template);
            return new Response($template->render($data));
        } else {
            // templated by filename
            return new Response($this->twig->render('tests/result/' . ResultUtil::resolveViewName($test) . '.html.twig', $data));
        }
    }
}