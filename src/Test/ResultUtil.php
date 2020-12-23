<?php
/**
 * @author: adavydov
 * @since: 05.11.2020
 */

namespace App\Test;


use App\Entity\Result;
use App\Entity\Test;

class ResultUtil
{
    /*
     * This is a depth of casual single result such as
     * {"questionId": ["value1", "value2"]...}
     * */
    const SINGLE_RESULT_DEPTH = 3;

    /**
     * Single result:
     * {"1": ["1", "2"]}
     * Complex result:
     * {"1": {"1": ["1", "2"]}}
     * @param Result $result
     * @return bool
     */
    public static function isComplex(Result $result): bool
    {
        // json_decode returns false, if the depth is deeper
        // than self::SINGLE_RESULT_DEPTH, which is complex result
        return json_decode($result->getData(), true, self::SINGLE_RESULT_DEPTH) == false;
    }

    public static function resolveViewName(Test $test)
    {
        return $test->getXmlFilename() ?? $test->getId();
    }
}