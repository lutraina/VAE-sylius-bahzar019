<?php

declare(strict_types=1);

namespace Tests\App\Sylius;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Currency\Model\Currency;

class ChannelTest extends TestCase
{
    public function testChannelCreation(): void
    {
        $channel = new Channel();
        $channel->setCode('WEB_US');
        $channel->setName('Web Store USA');

        $this->assertEquals('WEB_US', $channel->getCode());
        $this->assertEquals('Web Store USA', $channel->getName());
    }

    public function testChannelCurrency(): void
    {
        $channel = new Channel();
        $currency = new Currency();
        $currency->setCode('USD');

        $channel->setBaseCurrency($currency);

        $this->assertEquals('USD', $channel->getBaseCurrency()->getCode());
    }
}
