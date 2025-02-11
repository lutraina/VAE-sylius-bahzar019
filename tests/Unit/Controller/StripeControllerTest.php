<?php

namespace App\Tests\Controller;

use App\Controller\StripeController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sylius\Component\Order\Model\OrderInterface;
use App\Service\MelhorEnvioApiService;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class StripeControllerTest extends WebTestCase
{
    private $client;
    private $melhorEnvioApiService;
    private $cartContextMock;

    protected function setUp(): void
    {
        // Crée un client pour les tests
        $this->client = static::createClient();

        // Mock du service MelhorEnvioApiService
        $this->melhorEnvioApiService = $this->createMock(MelhorEnvioApiService::class);

        // Mock du CartContext
        $this->cartContextMock = $this->createMock(CartContextInterface::class);
    }

    public function testCreateCheckoutSession(): void
    {
        // Simule la création d'une session de paiement Stripe
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->method('getUrl')->willReturn('https://checkout.stripe.com/some_url');

        // Assurez-vous que la méthode create de Stripe renvoie une session simulée
        Session::method('create')->willReturn($sessionMock);

        // Crée une instance de StripeController avec des mocks
        $controller = new StripeController($this->cartContextMock, $this->melhorEnvioApiService);

        // Teste que la méthode renvoie une redirection vers Stripe
        $response = $controller->createCheckoutSession();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('https://checkout.stripe.com/some_url', $response->getTargetUrl());
    }

    public function testCheckoutWithEmptyCart(): void
    {
        // Simule un panier vide
        $this->cartContextMock->method('getCart')->willReturn(null);

        $controller = new StripeController($this->cartContextMock, $this->melhorEnvioApiService);

        // Teste que l'on est redirigé vers le résumé du panier si le panier est vide
        $response = $controller->checkout();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('sylius_shop_cart_summary', $response->getTargetUrl());
    }

    public function testCheckoutWithItemsInCart(): void
    {
        // Simule un panier avec des items
        $cartMock = $this->createMock(OrderInterface::class);
        $this->cartContextMock->method('getCart')->willReturn($cartMock);

        // Mock des éléments du panier
        $cartMock->method('getItems')->willReturn([]);

        $controller = new StripeController($this->cartContextMock, $this->melhorEnvioApiService);

        // Teste que la méthode crée correctement une session de paiement Stripe
        $response = $controller->checkout();
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('checkout.stripe.com', $response->getTargetUrl());
    }

    public function testSuccessPage(): void
    {
        // Teste si la page de succès est rendue correctement
        $crawler = $this->client->request('GET', '/checkout/stripe/success');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Paiement réussi');
    }

    public function testCancelPage(): void
    {
        // Teste si la page d'annulation est rendue correctement
        $crawler = $this->client->request('GET', '/checkout/stripe/cancel');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Paiement annulé');
    }
}
