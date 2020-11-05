<?php
/**
 * @author: adavydov
 * @since: 05.11.2020
 */

namespace App\Test;


use App\Entity\Test;

class ResultUtil
{
    public static function resolveViewName(Test $test)
    {
        return $test->getXmlFilename() ?? $test->getId();
    }
}