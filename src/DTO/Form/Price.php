<?php


namespace App\DTO\Form;


use Assert\Assertion;
use Assert\AssertionFailedException;

final class Price
{
    /**
     * @var string
     */
    private $value;

    /**
     * Price constructor.
     * @param string $value
     * @throws AssertionFailedException
     */
    public function __construct(string $value)
    {
        Assertion::notEmpty($value, 'Цена не может быть пустой');
        Assertion::numeric($value, 'Цена должна быть числом');
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
