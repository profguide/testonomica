<?php
/**
 * @author: adavydov
 * @since: 24.01.2021
 */

namespace App\Test\Calculator;

class ProfTeenTrialCalculator extends AbstractProforientationCalculator
{
    protected function getProfessionsFileName(): string
    {
        return $this->kernel->getProjectDir() . "/xml/proforientationTeenProfessions.xml";
    }

    public function calculate(): array
    {
        $this->fitVersion();
        $typesGroupsPercent = $this->calculateTypesGroups();
        $typesSinglePercent = $this->sumTypesGroups($typesGroupsPercent);
        return [
            'types_group_percent' => $typesGroupsPercent,
            'types_single_percent' => $typesSinglePercent,
            'types_descriptions' => $this->typesDescriptions($typesGroupsPercent),
            'types_top' => $this->grabTopTypes($typesSinglePercent),
        ];
    }

    /**
     * Из ответов формирует массив с процентами вида ['tech' => [33, 20, 50], 'body' => [0, 50, 0]]
     * @return array
     */
    public function calculateTypesGroups(): array
    {
        $types = ['natural'];
        $result = [];
        foreach ($types as $type) {
            $result[$type] = $this->calculateTypeGroups($type, $this->answersHolder, $this->questionsHolder);
        }
        return $result;
    }
}