<?php
/**
 * @author: adavydov
 * @since: 01.11.2020
 */

namespace App\Util;

use App\Entity\Answer;

class AnswersUtil
{
    public static function toJson(Answer $answer): string
    {
        return json_encode($answer);
    }
}