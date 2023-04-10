<?php

namespace App\Tests\Unit;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Form\ProfileType;
use Symfony\Component\Form\Test\TypeTestCase;

class CustomerTypeTest extends TypeTestCase
{
    public function testValidCustomerType(): void
    {
        $formData = [
            'email' => 'elmahdi.benbrahim@gmail.com',
        ];

        $customer = new Customer();

        $form = $this->factory->create(ProfileType::class, $customer);

        $form->submit($formData);

        $this->assertTrue($form->isValid());

        $this->assertEquals($customer, $form->getData());
    }

    public function testInvalidCustomerType(): void
    {
        $formData = [
            'email' => '',
        ];

        $customer = new Customer();

        $form = $this->factory->create(ProfileType::class, $customer);

        $form->submit($formData);

        $this->assertFalse($form->isValid());

        $errors = $form->getErrors(true);

        $this->assertCount(1, $errors);

        $this->assertEquals('L\'adresse email est obligatoire', $errors->current()->getMessage());
    }
}
