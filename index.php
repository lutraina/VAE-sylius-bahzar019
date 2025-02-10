<?php
// Inclure le SDK Stripe
require 'vendor/autoload.php';

// Clé API Stripe
\Stripe\Stripe::setApiKey('sk_test_51QlBCyCIDRpMCJERCO6B28LMuCHoO251uTqb3sN3b7SjvPeFzHj0SV1f362GvlojC7ccRSjcvSzqAblbCOzlpILM002oJxK98T');

// Créer une session de paiement
$session = \Stripe\Checkout\Session::create([
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
    'success_url' => 'http://localhost/success.php',
    'cancel_url' => 'http://localhost/cancel.php',
]);

// Rediriger vers la page de paiement Stripe
header("Location: " . $session->url);
exit();
