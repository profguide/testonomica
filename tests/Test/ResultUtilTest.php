<?php
/**
 * @author: adavydov
 * @since: 22.12.2020
 */

namespace App\Tests\Test;


use App\Entity\Result;
use App\Test\ResultUtil;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ResultUtilTest extends KernelTestCase
{
    public static function testIsComplex()
    {
        // single
        $singleResult = new Result();
        $singleResult->setData('{"1": ["1", "2"], "2": [0]}');
        self::assertFalse(ResultUtil::isComplex($singleResult));

        // complex
        $singleResult = new Result();
        $singleResult->setData('{"1": {"1": ["1", "2"], "2": [0]}}');
        self::assertTrue(ResultUtil::isComplex($singleResult));
    }
}