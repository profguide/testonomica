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

    private $description = [];

    private $rating = 0;

    public function __construct(string $name, array $combs, $not = [], $description = [])
    {
        $this->name = $name;
        $this->combs = $combs;
        $this->not = $not;
        $this->description = $description;
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

    /**
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     */
    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return array
     */
    public function getDescription(): array
    {
        return $this->description;
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