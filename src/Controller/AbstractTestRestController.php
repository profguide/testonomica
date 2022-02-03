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

    protected function getTest(int $id): Test
    {
        $test = $this->tests->findOneById($id);
        if (!$test) {
            throw self::createNotFoundException();
        }
        return $test;
    }
}