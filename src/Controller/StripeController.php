<?php

// src/Controller/StripeController.php

namespace App\Controller;

use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\MelhorEnvioApiService;

class StripeController extends AbstractController
{

    private CartContextInterface $cartContext;
    private MelhorEnvioApiService $melhorEnvioApiService;

    public function __construct(CartContextInterface $cartContext, MelhorEnvioApiService $melhorEnvioApiService)
    {
        $this->cartContext = $cartContext;
        $this->melhorEnvioApiService = $melhorEnvioApiService;
    }

    /**
     * @Route("/create-checkout-session", name="create_checkout_session")
     */
    public function createCheckoutSession()
    {
        // Inclure le SDK Stripe
        \Stripe\Stripe::setApiKey('sk_test_51QlBCyCIDRpMCJERCO6B28LMuCHoO251uTqb3sN3b7SjvPeFzHj0SV1f362GvlojC7ccRSjcvSzqAblbCOzlpILM002oJxK98T');

        // Créer une session de paiement
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'brl',
                        'product_data' => [
                            'name' => 'T-shirt',
                        ],
                        'unit_amount' => 2000, // Montant en cents (ex. 2000 = 20,00 USD)
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => 'http://localhost:8080/success',
            'cancel_url' => 'http://localhost:8080/cancel',
        ]);

        // Rediriger vers la page de paiement Stripe
        return new RedirectResponse($session->url);
    }


    /**
     * @Route("/checkout/stripe", name="stripe_checkout")
     */
    public function checkout(): Response
    {
        // Initialiser Stripe
        Stripe::setApiKey('sk_test_51QlBCyCIDRpMCJERCO6B28LMuCHoO251uTqb3sN3b7SjvPeFzHj0SV1f362GvlojC7ccRSjcvSzqAblbCOzlpILM002oJxK98T');

        // Récupérer le panier
        /** @var OrderInterface $cart */
        $cart = $this->cartContext->getCart();

        if ($cart->isEmpty()) {
            return $this->redirectToRoute('sylius_shop_cart_summary');
        }

        $lineItems = [];

        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'brl',
                    'product_data' => [
                        'name' => $product->getName(),
                    ],
                    'unit_amount' => intval($item->getUnitPrice()), // En cents
                ],
                'quantity' => $item->getQuantity(),
            ];
        }

        // Calcul des frais de livraison avec Melhor Envio
        $shippingData = [
            "from" => ["postal_code" => "13015-301"],
            "to" => ["postal_code" => "13026-540"],
            "products" => array_map(function ($item) {
                return [
                    "id" => "x",
                    "width" => 11,
                    "height" => 17,
                    "length" => 11,
                    "weight" => 0.3,
                    "insurance_value" => 10.1,
                    "quantity" => $item['quantity']
                ];
            }, $lineItems),
            "options" => ["receipt" => false, "own_hand" => false],
            "services" => "1,2,18"
        ];

        $shippingResponse = $this->melhorEnvioApiService->calculateShipment($shippingData);

        // Vérifier si on a bien un prix pour SEDEX
        $sedexPrice = null;
        foreach ($shippingResponse as $option) {
            if (isset($option['name']) && stripos($option['name'], 'SEDEX') !== false) {
                $sedexPrice = floatval($option['price'] ?? 0);
                break;
            }
        }

        // Ajouter les frais de livraison si SEDEX est disponible
        if ($sedexPrice !== null) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'brl',
                    'product_data' => [
                        'name' => 'Frais de livraison (SEDEX)',
                    ],
                    'unit_amount' => intval($sedexPrice * 100),
                ],
                'quantity' => 1,
            ];
        }

        // Vérification avant Stripe
        if (empty($lineItems)) {
            return $this->redirectToRoute('sylius_shop_cart_summary');
        }

        // Créer la session Stripe
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('stripe_success', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('stripe_cancel', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return new RedirectResponse($session->url);
    }



    /**
     * @Route("/checkout/stripe/success", name="stripe_success")
     */
    public function success(): Response
    {
        $header = 'Paiement réussi'; // Définir un header pour la page de succès
        return $this->render('payment/success.html.twig', [
            'header' => $header, // Passer la variable header
        ]);
        //return $this->render('payment/success.html.twig');
    }

    /**
     * @Route("/checkout/stripe/cancel", name="stripe_cancel")
     */
    public function cancel(): Response
    {
        $header = 'Paiement annulé'; // Définir un header pour la page d'annulation
        return $this->render('payment/cancel.html.twig', [
            'header' => $header, // Passer la variable header
        ]);
        //return $this->render('payment/cancel.html.twig');
    }



}

