<?php

declare(strict_types=1);

namespace App\Command;

use App\Kernel;
use DOMDocument;
use DOMNode;
use DOMNodeList;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:sort-proftest')]
final class SortProftestCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->sort();
        return self::SUCCESS;
    }

    private function sort()
    {
        // загрузка XML-файла
        $doc = new DOMDocument();
        $doc->load('xml/proftest/proftest_source.xml');

        // получение списка вопросов
        $questions = $doc->getElementsByTagName('item');


        // сортировка вопросов по типу
        $sortedQuestions = $this->sortQuestions($questions);

        // создание нового XML-файла с отсортированными вопросами
        $newDoc = new DOMDocument('1.0', 'UTF-8');
        $newDoc->appendChild($newDoc->createComment('GENERATED, DO NOT FIX IT AS YOUR CHANGES WILL BE ERASED'));

        $newDoc->appendChild($newDoc->createElement('quiz'));
        $items = $newDoc->documentElement->appendChild($newDoc->createElement('items'));
        foreach ($sortedQuestions as $question) {
            $items->appendChild($newDoc->importNode($question, true));
        }
        $newDoc->formatOutput = true;
        $newDoc->save('xml/proftest/quiz.xml');
    }

    private function sortQuestions(DOMNodeList $questions): array
    {
        // определение порядка вопросов на основе типа
        // например, вопрос имеет тип "легкий", "сложный", "интересный" и тд.
        // и тогда можно здесь указать в каком порядке их сортировать
        $typeOrder = [
            'top' => 0,
            'interesting' => 1,
            'easy' => 2,
            'difficult' => 3,
        ];

        $sortedItems = iterator_to_array($questions);
        usort($sortedItems, function ($a, $b) use ($typeOrder) {
            /**@var DOMNode $a */
            /**@var DOMNode $b */
            $a_type = $a->getAttribute('qtype');
            $b_type = $b->getAttribute('qtype');
////            if (empty($b_type) && empty($b_type)) {
////                return -1;
////            }
//            if (empty($a_type)) {
//                $a_type = 1000;
//            }
//            if (empty($b_type)) {
//                $b_type = 1000;
//            }
//
//            return $a_type <=> $b_type;

            if (isset($typeOrder[$a_type]) && isset($typeOrder[$b_type])) {
                return $typeOrder[$a_type] - $typeOrder[$b_type];
            } elseif (isset($typeOrder[$a_type])) {
                return -1;
            } elseif (isset($typeOrder[$b_type])) {
                return 1;
            } else {
                return strcmp($a_type, $b_type);
            }
        });

        return $sortedItems;
    }
}