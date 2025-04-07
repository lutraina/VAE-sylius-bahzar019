<?php


// src/Controller/Admin/WishlistController.php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\WishlistService;

// Assurez-vous d'avoir un service de gestion de wishlist

class WishlistController extends AbstractController
{
    private WishlistService $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    /**
     * @Route("/admin/wishlist", name="admin_wishlist_show")
     */
    public function showWishlist(): Response
    {
        // Exemple de récupération des éléments de la wishlist pour l'utilisateur actuel
        // Si vous avez un service de wishlist, utilisez-le pour obtenir les éléments
        // $wishlist = $this->wishlistService->getWishlistForAdmin();

        //$wishlist = $this->wishlistService->getWishlist();
        $wishlist = 1;
        // Passez la variable 'wishlist' à la vue
        return $this->render('@SyliusAdmin/Wishlist/show.html.twig', [
            'wishlist' => $wishlist,
        ]);
    }
}
