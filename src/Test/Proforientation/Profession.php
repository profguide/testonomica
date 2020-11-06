<?php
/**
 * @author: adavydov
 * @since: 06.11.2020
 */

namespace App\Test\Proforientation;


class Profession
{
    private $name;

    private $combs;

    public function __construct(string $name, array $combs)
    {
        $this->name = $name;
        $this->combs = $combs;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCombs()
    {
        return $this->combs;
    }
}