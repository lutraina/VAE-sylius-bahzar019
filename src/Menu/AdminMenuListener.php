<?php


namespace App\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AdminMenuListener
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        // Ajouter un sous-menu "Wishlist"
        $newSubmenu = $menu
            ->addChild('wishlist')
            ->setLabel('Wishlist')
        ;

        // Générer l'URL pour la route 'admin_wishlist_show' qui correspond à la page d'admin wishlist
        $url = $this->urlGenerator->generate('admin_wishlist_show');

        // Ajouter un sous-item pour la wishlist admin
        $newSubmenu
            ->addChild('wishlist_manage')
            ->setLabel('Manage Wishlist')
            ->setUri($url)  // Utiliser l'URL générée pour rediriger
        ;
    }
}
