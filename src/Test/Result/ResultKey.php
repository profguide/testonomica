<?php

namespace App\Test\Result;

use Symfony\Component\Uid\Uuid;

interface ResultKey
{
    function getValue(): Uuid|string;
}