<?php

declare(strict_types=1);

namespace Tests\App\Sylius;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\Component\Payment\Model\PaymentInterface;

class PaymentTest extends TestCase
{
    public function testPaymentCreation(): void
    {
        $payment = new Payment();
        $payment->setState(PaymentInterface::STATE_NEW);

        $this->assertEquals(PaymentInterface::STATE_NEW, $payment->getState());
    }

    public function testPaymentMethodAssignment(): void
    {
        $payment = new Payment();
        $paymentMethod = new PaymentMethod();
        $paymentMethod->setCode('PAYPAL');

        $payment->setMethod($paymentMethod);

        $this->assertEquals('PAYPAL', $payment->getMethod()->getCode());
    }
}
