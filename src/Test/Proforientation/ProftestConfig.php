<?php
/**
 * @author: adavydov
 * @since: 24.11.2020
 */

namespace App\Test\Proforientation;

use App\Test\Proforientation\Type\TypesCollection;

class ProftestConfig
{
    private TypesCollection $types;

    public function __construct(TypesCollection $types)
    {
        $this->types = $types;
    }

    public function types(): TypesCollection
    {
        return $this->types;
    }
}