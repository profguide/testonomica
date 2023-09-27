<?php

declare(strict_types=1);

namespace App\V2\Provider\Policy\Test;

enum TestPolicy: string
{
    case ONE_PROFTEST = 'one_proftest';
    case ONE_PROFTEST_ONE_BONUS = 'one_proftest_one_bonus';
    case UNLIMITED_PROFTEST = 'unlimited_proftest';
}