<?php

declare(strict_types=1);

namespace Tests\YourProject\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    private $productIndexPage;
    private $productCreatePage;
    private $notificationChecker;

    public function __construct(
        SymfonyPage $productIndexPage,
        SymfonyPage $productCreatePage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->productIndexPage = $productIndexPage;
        $this->productCreatePage = $productCreatePage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I am on the product creation page
     */
    public function iAmOnTheProductCreationPage(): void
    {
        $this->productCreatePage->open();
    }

    /**
     * @When I create a new product named :name
     */
    public function iCreateANewProductNamed(string $name): void
    {
        $this->productCreatePage->fillField('Name', $name);
        $this->productCreatePage->pressButton('Create');
    }

    /**
     * @Then I should see the product :name in the admin panel
     */
    public function iShouldSeeTheProductInTheAdminPanel(string $name): void
    {
        $this->productIndexPage->open();
        Assert::true($this->productIndexPage->isProductOnTheList($name), "Le produit '$name' n'est pas visible.");
    }
}
