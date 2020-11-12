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

    public function hasComb(string $str)
    {
        foreach ($this->combs as $comb) {
            if ($str == implode(",", $comb)) {
                return true;
            }
        }
    }

    public function combsString()
    {
        $combs = [];
        foreach ($this->combs as $k => $comb) {
            $combs[] = implode(",", $comb);
        }
        return implode("\n", $combs);
    }

    public function anyTypeList(string $str)
    {
        $typesNeed = explode(',', $str);
        $list = [];
        foreach ($this->combs as $comb) {
            $cross = array_intersect($comb, $typesNeed);
            if (!empty($cross)) {
                $crossStr = implode(',', $cross);
                if (!in_array($crossStr, $list)) {
                    $list[] = $crossStr;
                }
            }
        }
        return implode(';', $list);
    }
}