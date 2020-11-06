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

    private $not = [];

    public function __construct(string $name, array $combs, $not = [])
    {
        $this->name = $name;
        $this->combs = $combs;
        $this->not = $not;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCombs()
    {
        return $this->combs;
    }

    public function getNot()
    {
        return $this->not;
    }
}