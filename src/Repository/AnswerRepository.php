<?php
/**
 * @author: adavydov
 * @since: 31.10.2020
 */

namespace App\Repository;


use App\Entity\Answer;
use App\Entity\Test;
use App\Test\AnswersSerializer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AnswerRepository
{
    private const SESS_NAME = 'test';

    /**@var Answer[] */
    private $data;

    /**@var SessionInterface */
    private $session;

    /**@var AnswersSerializer */
    private $serializer;

    public function __construct(SessionInterface $session, AnswersSerializer $serializer)
    {
        $this->session = $session;
        $this->serializer = $serializer;
    }

    public function save(Test $test, Answer $answer): void
    {
        $data = $this->loadFromSession($test);
        $data[$answer->getQuestionId()] = $answer;
        $this->saveToSession($test, $data);
    }

    public function getAll(Test $test): array
    {
        return $this->loadFromSession($test);
    }

    public function clear(Test $test): void
    {
        $this->saveToSession($test, []);
    }

    public function count(Test $test): int
    {
        return count($this->loadFromSession($test));
    }

    public function getLastIdByTest(Test $test): ?int
    {
        return array_key_last($this->loadFromSession($test));
    }

    /**
     * @param Test $test
     * @return Answer[]
     */
    private function loadFromSession(Test $test): array
    {
        if ($this->data != null) {
            return $this->data;
        }
        $stringData = $this->session->get(self::sessionTestKey($test));
        $parsedData = null;
        if ($stringData == null) {
            $parsedData = [];
        } else {
            $parsedData = $this->serializer->deserialize($stringData);
        }
        $this->data = $parsedData;
        return $parsedData;
    }

    /**
     * @param Test $test
     * @param array $data
     */
    private function saveToSession(Test $test, array $data): void
    {
        $this->data = $data;
        $serializedData = $this->serializer->serialize($data);
        $this->session->set(self::sessionTestKey($test), $serializedData);
    }

    private static function sessionTestKey(Test $test)
    {
        return self::SESS_NAME . "-" . $test->getId();
    }
}