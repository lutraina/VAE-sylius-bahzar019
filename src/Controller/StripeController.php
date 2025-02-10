<?php

// src/Controller/StripeController.php

namespace App\Controller;

use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
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
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'T-shirt',
                        ],
                        'unit_amount' => 2000, // Montant en cents (ex. 2000 = 20,00 USD)
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => 'http://localhost:8000/success',
            'cancel_url' => 'http://localhost:8000/cancel',
        ]);

        // Rediriger vers la page de paiement Stripe
        return new RedirectResponse($session->url);
    }
}

