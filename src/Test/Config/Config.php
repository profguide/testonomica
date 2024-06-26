<?php

declare(strict_types=1);

namespace App\Test\Config;

use App\Test\Config\Struct\Scenario;
use App\Tests\Test\Config\ConfigTest;

/**
 * Value-object, stores nested array and helps to get to a value by chain.
 * It was specially developed for tests config.xml
 *
 * @see ConfigTest
 */
final class Config
{
    /**
     * @param Scenario[] $scenarios
     * @param array $data
     */
    public function __construct(public array $scenarios, private array $data)
    {
    }

    /**
     * @param string $key format: %config.min.text%
     */
    public function get(string $key, bool $required = true): ?string
    {
        $_key = str_replace('%config.', '', $key);
        $_key = rtrim($_key, '%');
        $keys = explode('.', $_key);

        $value = $this->data;
        foreach ($keys as $_key) {
            if (isset($value[$_key])) {
                $value = $value[$_key];
            } else {
                if ($required) {
                    throw new \RuntimeException("Cant find value \"$key\" in config.");
                }
                return null;
            }
        }

        return $value;
    }

    public function getAllVariables(): array
    {
        return $this->data;
    }
}