<?php

declare(strict_types=1);

namespace App\Test\Config;

/**
 * Value-object, contains translated text from config.xml
 */
final class Config
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $key format: %config.min.text%
     * @return void
     */
    public function get(string $key): string
    {
        $key = ltrim($key, '%config.');
        $key = rtrim($key, '%');
        $keys = explode('.', $key);

        $value = $this->data;
        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                throw new \RuntimeException("Cant find value \"$key\" in config.");
            }
        }

        return $value;
    }
}