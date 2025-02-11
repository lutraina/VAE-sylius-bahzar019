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

class StripeController extends AbstractController
{

    private CartContextInterface $cartContext;

    public function __construct(CartContextInterface $cartContext, )
    {
        $this->cartContext = $cartContext;

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
        // Initialiser Stripe avec votre clé secrète
        Stripe::setApiKey('sk_test_51QlBCyCIDRpMCJERCO6B28LMuCHoO251uTqb3sN3b7SjvPeFzHj0SV1f362GvlojC7ccRSjcvSzqAblbCOzlpILM002oJxK98T');

        // Récupérer le panier de l'utilisateur
        /** @var OrderInterface $cart */
        $cart = $this->cartContext->getCart();


        if ($cart->isEmpty()) {
             // dd($cart);die;
            return $this->redirectToRoute('sylius_shop_cart_summary');
        }

        $lineItems = [];

        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'brl', // Remplacez par la devise de votre boutique
                    'product_data' => [
                        'name' => $product->getName(),
                    ],
                    'unit_amount' => intval($item->getUnitPrice()), // Stripe attend un montant en cents
                ],
                'quantity' => $item->getQuantity(),
            ];
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

        // Passer la variable header pour le template
       /*$header = 'Votre panier';
        return $this->render('cart/index.html.twig', [
            'header' => $header, // Transmission de la variable header
            'cart' => $cart,     // Transmission du panier
            // Ajoutez d'autres variables nécessaires ici
        ]);*/
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

