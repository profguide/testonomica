<?php

namespace App\Test\Calculator;

use App\Test\AbstractCalculator;

class AutoCalculator extends AbstractCalculator
{
    function calculate(): array
    {
        return [
            'stub_key' => 'stub_value'
        ];
    }
}