<?php
/**
 * @author: adavydov
 * @since: 01.11.2020
 */

namespace App\Test;


use Symfony\Component\Serializer\SerializerInterface;

class AnswersSerializer
{
    /**@var SerializerInterface */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serialize(array $answers): string
    {
        return $this->serializer->serialize($answers, 'json');
    }

    public function deserialize(string $json): array
    {
        return $this->serializer->deserialize($json, 'App\Entity\Answer[]', 'json');
    }
}