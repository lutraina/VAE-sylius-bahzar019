<?php
namespace App\Tests\Unit\Entity\Product;
use App\Entity\Locale\Locale;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Calculator\PriceCalculator;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelPricing;
use Sylius\Component\Core\Model\Product;
//use App\Entity\Product\ProductTranslation;
use Sylius\Component\Core\Model\ProductTranslation;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Taxation\Model\TaxCategory;
use Sylius\Component\Product\Model\ProductTranslationInterface;

//use App\Entity\Product\Product;
//use PHPUnit\Framework\TestCase;


class ProductTest extends TestCase {


    public function testCreateTranslation(): void
    {
        $product = new class extends Product {
            public function publicCreateTranslation(): ProductTranslationInterface
            {
                return $this->createTranslation();
            }
        };

        $translation = $product->publicCreateTranslation();
        var_dump(get_class($translation));
        $this->assertInstanceOf(ProductTranslationInterface::class, $translation);
        $this->assertInstanceOf(ProductTranslation::class, $translation);
    }

    public function testCanGetAndSetData3(): void
    {
        $locale = new Locale();
        $locale->setCode('pt_BR');
        $product = new Product();
        $product->setCurrentLocale($locale);

        $product->setName('Lu');
        $product->setCreatedAt(new \DateTime('now'));
        $product->setCode('009');

        self::assertSame('Lu', $product->getName());
        self::assertSame('009', $product->getCode());
        self::assertSame('pt_BR',  $locale->getCode());
    }

    public function testCanGetAndSetData2(): void
    {
        $productName = 'Lu';
        $code = '009';
        $date = new \DateTime('now');

        // Création d'un mock de ProductInterface
        $product = $this->createMock(ProductInterface::class);

        // Simulation du comportement des méthodes
        $product->expects(self::once())
            ->method('getName')
            ->willReturn($productName);

        $product->expects(self::once())
            ->method('setCreatedAt')
            ->with($date);

        $product->expects(self::once())
            ->method('setCode')
            ->with($code);

        // Exécution des méthodes
        $product->setCreatedAt($date);
        $product->setCode($code);

        // Vérifications
        self::assertSame('Lu', $product->getName());
    }

    public function testProductCreation(): void
    {
        $product = new Product();
        $product->setCode('TEST_PRODUCT');
        $product->setCurrentLocale('en_US');
        $product->setName('Test Product');

        $this->assertEquals('TEST_PRODUCT', $product->getCode());
        $this->assertEquals('Test Product', $product->getName());
    }

    public function testProductVariant(): void
    {
        $product = new Product();
        $variant = new ProductVariant();
        $variant->setCode('VARIANT_1');
        $product->addVariant($variant);

        $this->assertCount(1, $product->getVariants());
        $this->assertEquals('VARIANT_1', $product->getVariants()->first()->getCode());
    }

    public function testProductTaxCategory(): void
    {
        $variant = new ProductVariant();
        $taxCategory = new TaxCategory();
        $taxCategory->setCode('STANDARD_TAX');

        $variant->setTaxCategory($taxCategory);

        $this->assertEquals('STANDARD_TAX', $variant->getTaxCategory()->getCode());
    }

    public function testProductPriceCalculation(): void
    {
        $variant = new ProductVariant();
        $variant->setCode('VARIANT_1');

        $channel = new Channel();
        $channel->setCode('WEB_US');

        $pricing = new ChannelPricing();
        $pricing->setChannelCode($channel->getCode());
        $pricing->setPrice(1000); // Prix en centimes

        $variant->addChannelPricing($pricing);

        $this->assertEquals(1000, $variant->getChannelPricingForChannel($channel)->getPrice());
    }

    public function testProductAvailability(): void
    {
        $product = new Product();
        $product->setEnabled(true);

        $this->assertTrue($product->isEnabled());
    }

}
