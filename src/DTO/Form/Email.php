<?php


namespace App\DTO\Form;


use Assert\Assertion;
use Assert\AssertionFailedException;

final class Email
{
    /**
     * @var string
     */
    private $value;

    /**
     * Email constructor.
     * @param string $value
     * @throws AssertionFailedException
     */
    public function __construct(string $value)
    {
        Assertion::notEmpty($value, 'Email не может быть пустым');
        Assertion::email($value, 'Email должен быть валидным email адресом');
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }


    public function __toString(): string
    {
        return $this->getValue();
    }

}
