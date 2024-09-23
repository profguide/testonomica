<?php

declare(strict_types=1);

namespace App\Controller\Partner\Api\V3\Extractor;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractParamExtractor
{
    function getStringRequestParam(Request $request, string $paramName)
    {
        // 1. Попытка получить параметр через стандартный метод
        $paramValue = $request->get($paramName);

        // 2. Если параметр отсутствует, проверяем содержимое тела JSON
        if (!$paramValue) {
            $content = $request->getContent();
            if (!empty($content)) {
                $data = json_decode($content, true);

                // Проверяем на ошибки при декодировании
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // Если JSON невалидный, просто игнорируем это
                }

                $paramValue = $data[$paramName] ?? null;
            }
        }

        return $paramValue; // Вернем обычное значение (строка или null)
    }

    protected function getJsonRequestParam(Request $request, string $paramName): array
    {
        $paramValue = $request->get($paramName);

        if (!$paramValue) {
            $content = $request->getContent();
            if (!empty($content)) {
                $data = json_decode($content, true);

                // Проверяем, не произошла ли ошибка при декодировании JSON
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new BadRequestException('Invalid JSON provided: ' . json_last_error_msg());
                }

                $paramValue = $data[$paramName] ?? null;
            }
        }

        // Проверка на строку и попытка декодировать её как массив
        if (is_string($paramValue)) {
            $decodedValue = json_decode($paramValue, true);

            // Проверяем, не произошла ли ошибка при декодировании JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new BadRequestException('Invalid JSON provided: ' . json_last_error_msg());
            }

            if (is_array($decodedValue)) {
                $paramValue = $decodedValue;
            }
        }

        return $paramValue;
    }
}