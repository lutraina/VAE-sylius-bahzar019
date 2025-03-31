<?php

declare(strict_types=1);

namespace Tests\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Doctrine\Persistence\ObjectManager;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Context\Setup\ProductTaxonContext;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;
use Behat\MinkExtension\Context\MinkContext;  // Importer MinkContext
use Behat\Step\Given;
use Behat\Step\When;
use Behat\Step\Then;
use Sylius\Behat\Service\SharedStorageInterface;


final class ProductContext extends MinkContext implements Context
{
    private $productIndexPage;
    private $productCreatePage;
    private $notificationChecker;
    private  $sharedStorage;
    private $productContext;


    public function setSharedStorageInterface(SharedStorageInterface $sharedStorage): void
    {
        $this->sharedStorage = $sharedStorage;
    }
    // Setter methods for dependency injection
    public function setProductIndexPage(SymfonyPage $productIndexPage): void
    {
        $this->productIndexPage = $productIndexPage;
    }

    public function setProductCreatePage(SymfonyPage $productCreatePage): void
    {
        $this->productCreatePage = $productCreatePage;
    }

    public function setNotificationChecker(NotificationCheckerInterface $notificationChecker): void
    {
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given the store( also) has a product :productName with code :code
     * @Given the store( also) has a product :productName with code :code, created at :date
     */
    public function storeHasProductWithCode($productName, $code, $date = 'now')
    {
        $product = $this->createProduct($productName);
        $product->setCreatedAt(new \DateTime($date));
        $product->setCode($code);

        $this->saveProduct($product);
    }

    // Méthode pour se connecter en tant qu'administrateur
    #[Given('I am logged in as an administrator')]
    public function iAmLoggedInAsAnAdministrator(): void
    {
        // Code pour se connecter en tant qu'administrateur
        $this->getSession()->visit('http://localhost:8080/admin/login');
        $this->getSession()->getPage()->fillField('Username', 'lutraina');
        $this->getSession()->getPage()->fillField('Password', 'MariAgosto22%_');
        $this->getSession()->getPage()->pressButton('Login');
    }


    #[When('I create a new product named :arg1 :arg2')]
    public function iCreateANewProductNamed($arg1, $arg2, $date = 'now')
    {
        $product = $this->createProduct($arg1);
        $product->setCreatedAt(new \DateTime($date));
        $product->setCode($arg2);

        $this->saveProduct($product);
    }

    // Méthode pour vérifier qu'un produit est visible dans le panneau d'administration
    #[Then('I should see the product :arg1 in the admin panel')]
    public function iShouldSeeTheProductInTheAdminPanel(string $arg1): void
    {
        // Code pour vérifier que le produit est visible dans le panneau admin
        $this->productIndexPage->open();
        Assert::true($this->productIndexPage->isProductOnTheList($arg1), "Le produit '$arg1' n'est pas visible.");
    }

}
