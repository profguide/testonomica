<?php
/**
 * @author: adavydov
 * @since: 02.11.2020
 */

namespace App\Controller;


use App\Entity\Test;
use http\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tests/cli", name="test_cli.")
 * @package App\Controller
 * @author: adavydov
 * @since: 23.10.2020
 */
class TestApiStatelessController extends TestApiAbstract
{
    /**
     * @Route("/save/", name="save-results")
     */
    public function saveResults()
    {
        // todo
        throw new RuntimeException("Ending is not unsupported yet");
    }

    public function end(Test $test)
    {
        // todo
        throw new RuntimeException("Ending is not unsupported yet");
    }

    protected function saveAnswer(Test $test, string $questionId, string $value): void
    {
        return; // no state - not save
    }

    public function clear(Test $test)
    {
        throw new BadRequestHttpException("Unsupported operation");
    }

    public function restore(Test $test)
    {
        throw new BadRequestHttpException("Unsupported operation");
    }
}