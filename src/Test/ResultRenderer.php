<?php

namespace App\Test;

use App\Entity\Test;
use App\Service\AnalysisRenderer;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
     * @param Test $test
     * @param array $data
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(Test $test, array $data): Response
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