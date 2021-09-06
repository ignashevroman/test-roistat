<?php


namespace App\DTO\Form;


use Assert\Assertion;
use Assert\AssertionFailedException;

final class Phone
{
    /**
     * @var string
     */
    private $value;

    /**
     * Phone constructor.
     * @param string $value
     * @throws AssertionFailedException
     */
    public function __construct(string $value)
    {
        // TODO: Add phone validation
        Assertion::notEmpty($value, 'Номер телефона не может быть пустым');
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }
}
