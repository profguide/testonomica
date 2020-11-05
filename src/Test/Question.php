<?php
/**
 * @author: adavydov
 * @since: 25.10.2020
 */

namespace App\Test;


class Question
{
    const METHOD_OPTION = "OPTION";
    const METHOD_TEXT = "TEXT";
    const METHOD_CHECKBOX = "CHECKBOX";
    const METHOD_RATING = "RATING";

    private $id;

    private $name;

    private $text;

    private $method = self::METHOD_OPTION;

    private $group;

    private $img;

    private $options = [];

    private $fields = [];

    private $count;

    private $wrong;

    private $right;

    private $enabledBack = true;

    private $enabledForward = false;

    private $showAnswer = false;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod(?string $method): void
    {
        if ($method == null) {
            return;
        }
        if (!in_array($method, [self::METHOD_OPTION, self::METHOD_TEXT, self::METHOD_CHECKBOX, self::METHOD_RATING])) {
            throw new UnknownQuestionMethodException();
        }
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group): void
    {
        $this->group = $group;
    }

    /**
     * @return mixed
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * @param mixed $img
     */
    public function setImg($img): void
    {
        $this->img = $img;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count): void
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getWrong()
    {
        return $this->wrong;
    }

    /**
     * @param mixed $wrong
     */
    public function setWrong($wrong): void
    {
        $this->wrong = $wrong;
    }

    /**
     * @return mixed
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param mixed $right
     */
    public function setRight($right): void
    {
        $this->right = $right;
    }

    /**
     * @return bool
     */
    public function isEnabledBack(): bool
    {
        return $this->enabledBack;
    }

    /**
     * @param bool $enabledBack
     */
    public function setEnabledBack(bool $enabledBack): void
    {
        $this->enabledBack = $enabledBack;
    }

    /**
     * @return bool
     */
    public function isEnabledForward(): bool
    {
        return $this->enabledForward;
    }

    /**
     * @param bool $enabledForward
     */
    public function setEnabledForward(bool $enabledForward): void
    {
        $this->enabledForward = $enabledForward;
    }

    /**
     * @return bool
     */
    public function isShowAnswer(): bool
    {
        return $this->showAnswer;
    }

    /**
     * @param bool $showAnswer
     */
    public function setShowAnswer(bool $showAnswer): void
    {
        $this->showAnswer = $showAnswer;
    }

    /**
     * @param Option $option
     */
    public function addOption(Option $option): void
    {
        $this->options[] = $option;
    }

    /**
     * @param Field $field
     */
    public function addField(Field $field): void
    {
        $this->fields[] = $field;
    }

    public function getCorrectValues()
    {
        $values = [];
        if ($this->method == self::METHOD_TEXT) {
            /**@var Field $field */
            foreach ($this->fields as $field) {
                $values[] = $field->getCorrect();
            }
        } else {
            throw new \RuntimeException('Not supported for other types yet');
        }
        return $values;
    }
}