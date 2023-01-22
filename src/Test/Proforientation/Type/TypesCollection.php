<?php

declare(strict_types=1);

namespace App\Test\Proforientation\Type;

use App\Test\Proforientation\TypeConfig;

final class TypesCollection
{
    /**@var TypeConfig[] */
    private array $types = [];

    public function add(string $id, TypeConfig $type)
    {
        $this->types[$id] = $type;
    }

    public function get(string $id): TypeConfig
    {
        return $this->types[$id];
    }
}