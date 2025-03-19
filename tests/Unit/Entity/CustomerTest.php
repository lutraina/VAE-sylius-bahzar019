<?php

declare(strict_types=1);

namespace Tests\App\Sylius;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Customer\Model\Customer;
use Sylius\Component\Customer\Model\CustomerGroup;

class CustomerTest extends TestCase
{
    public function testCustomerCreation(): void
    {
        $customer = new Customer();
        $customer->setEmail('test@example.com');
        $customer->setFirstName('John');
        $customer->setLastName('Doe');

        $this->assertEquals('test@example.com', $customer->getEmail());
        $this->assertEquals('John', $customer->getFirstName());
        $this->assertEquals('Doe', $customer->getLastName());
    }

    public function testCustomerGroupAssignment(): void
    {
        $customer = new Customer();
        $group = new CustomerGroup();
        $group->setCode('VIP');

        $customer->setGroup($group);

        $this->assertEquals('VIP', $customer->getGroup()->getCode());
    }
}
