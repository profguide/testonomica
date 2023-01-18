<?php

declare(strict_types=1);

namespace App\Test\Proforientation;

use App\Tests\Test\Proforientation\ProfessionXmlValidationTest;

/**
 * Value-object
 * @see ProfessionXmlValidationTest
 */
final class ValueSystem
{
    const ALL = ['salary', 'big-company', 'prestige', 'travel', 'promotion', 'self-employ', 'people', 'work-alone', 'gov', 'benefit', 'art', 'indoor', 'outdoor', 'difference', 'publicity', 'safe', 'result', 'intel', 'hands', 'body', 'free-time', 'high-society', 'light-work'];

    private array $values;

    public function __construct(array $values)
    {
        // здесь специально нет проверки значений
        $this->values = $values;
    }

    public function values(): array
    {
        return $this->values;
    }
}