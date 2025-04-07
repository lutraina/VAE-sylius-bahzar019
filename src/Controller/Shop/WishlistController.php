<?php

// src/Controller/Shop/WishlistController.php
namespace App\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\WishlistService;
use Symfony\Component\Routing\Annotation\Route;


class WishlistController extends AbstractController
{
    private $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    /**
     * @Route("/whish-list", name="whish_list")
     */
    public function index(Request $request): Response
    {
        // Vous pouvez récupérer la wishlist de l'utilisateur ici si nécessaire
        //$wishlist = $this->get('sylius.shop_context')->getWishlist(); // Récupère la wishlist de l'utilisateur
        //$wishlistItems = $wishlist->getItems();

        return $this->render('/bundles/SyliusShopBundle/Wishlist/index.html.twig', [
            // 'wishlistItems' => $wishlistItems,
        ]);
    }

    /**
     * @Route("/wishlist/add/{productId}", name="wishlist_add")
     */
    public function addToWishlist(int $productId)
    {
        // Récupérer le produit (votre logique pour obtenir un produit par son ID)
        // Assurez-vous de récupérer le produit de manière correcte ici
        $product = //... logic to get product by ID

            // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();

        if (!$user) {
            // Rediriger vers la page de connexion ou afficher un message d'erreur
            return $this->redirectToRoute('app_login');
        }

        // Ajouter à la wishlist de l'utilisateur
        $this->wishlistService->addToWishlist($user, $product);

        // Redirection vers la page de wishlist
        return new RedirectResponse('/wishlist');
    }

    /**
     * @Route("/wishlist", name="wishlist_show")
     */
    public function showWishlist()
    {
        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();

        if (!$user) {
            // Rediriger vers la page de connexion ou afficher un message d'erreur
            return $this->redirectToRoute('app_login');
        }

        // Récupérer la wishlist de l'utilisateur
        $wishlist = $this->wishlistService->getWishlist($user);

        // Retourner la vue avec la wishlist
        return $this->render('wishlist/show.html.twig', [
            'wishlist' => $wishlist
        ]);
    }
}
