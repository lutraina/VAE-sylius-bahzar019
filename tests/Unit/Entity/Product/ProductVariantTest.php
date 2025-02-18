<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Product;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelPricing;
use Sylius\Component\Core\Model\ProductVariant;

class ProductVariantTest extends TestCase
{
    public function testProductVariantCreation(): void
    {
        $variant = new ProductVariant();
        $variant->setCode('VARIANT_1');

        $this->assertEquals('VARIANT_1', $variant->getCode());
    }

    public function testProductVariantChannelPricing(): void
    {
        $variant = new ProductVariant();

        $channel = new Channel();
        $channel->setCode('WEB_US');

        $pricing = new ChannelPricing();
        $pricing->setChannelCode($channel->getCode());
        $pricing->setPrice(1500); // Prix en centimes

        $variant->addChannelPricing($pricing);

        $this->assertEquals(1500, $variant->getChannelPricingForChannel($channel)->getPrice());
    }
}
