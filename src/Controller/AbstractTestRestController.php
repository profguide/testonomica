<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Test;
use App\Repository\TestRepository;
use App\Service\TestSourceService;

abstract class AbstractTestRestController extends AbstractRestController
{
    protected TestRepository $tests;

    protected TestSourceService $questions;

    public function __construct(TestRepository $tests, TestSourceService $questions)
    {
        $this->tests = $tests;
        $this->questions = $questions;
    }

    protected function getTest(string|int $id): Test
    {
        if (is_numeric($id)) {
            $test = $this->tests->findOneById((int)$id);
        } else {
            $test = $this->tests->findOneBySlug($id);
        }
        if (!$test) {
            throw self::createNotFoundException();
        }
        return $test;
    }
}