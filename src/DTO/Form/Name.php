<?php


namespace App\DTO\Form;


use Assert\Assertion;
use Assert\AssertionFailedException;

final class Name
{
    /**
     * @var string
     */
    private $value;

    /**
     * Name constructor.
     * @param string $value
     * @throws AssertionFailedException
     */
    public function __construct(string $value)
    {
        Assertion::notEmpty($value, 'Имя не может быть пустым');
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
