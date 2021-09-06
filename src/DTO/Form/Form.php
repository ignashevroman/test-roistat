<?php


namespace App\DTO\Form;


use Assert\AssertionFailedException;

final class Form
{
    /**
     * @var Name
     */
    private $name;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var Phone
     */
    private $phone;

    /**
     * @var Price
     */
    private $price;

    /**
     * Form constructor.
     * @param Name $name
     * @param Email $email
     * @param Phone $phone
     * @param Price $price
     */
    public function __construct(Name $name, Email $email, Phone $phone, Price $price)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->price = $price;
    }

    /**
     * @param array $data
     * @return Form
     * @throws AssertionFailedException
     */
    public static function fromArray(array $data): Form
    {
        return new self(
            new Name($data['name'] ?? ''),
            new Email($data['email'] ?? ''),
            new Phone($data['phone'] ?? ''),
            new Price($data['price'] ?? ''),
        );
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return Phone
     */
    public function getPhone(): Phone
    {
        return $this->phone;
    }

    /**
     * @return Price
     */
    public function getPrice(): Price
    {
        return $this->price;
    }
}
