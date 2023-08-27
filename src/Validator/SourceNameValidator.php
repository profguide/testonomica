<?php


namespace App\Validator;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * @see SourceName
 */
class SourceNameValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof SourceName) {
            throw new UnexpectedTypeException($constraint, SourceName::class);
        }

        if (empty($value)) {
            return;
        }

        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $value)) {
            $this->context->buildViolation('Значение должно содержать только латинские буквы и цифры')
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }

        if (!preg_match('/^[a-z]/', $value)) {
            $this->context->buildViolation('Значение должно начинаться с маленькой буквы')
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}