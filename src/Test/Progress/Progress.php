<?php

declare(strict_types=1);

namespace App\Test\Progress;

use App\Entity\Answer;

final readonly class Progress
{
    /***
     * @param Answer[] $answers
     */
    public function __construct(public array $answers)
    {
        if (get_class(reset($answers)) !== Answer::class) {
            throw new \DomainException('The array should consist only of objects of the Answer type.');
        }
    }

    public function hashSum(): string
    {
        return md5(serialize($this->answers));
    }
}