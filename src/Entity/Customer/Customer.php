<?php

declare(strict_types=1);

namespace App\Entity\Customer;

use App\Entity\Wishlist;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Customer as BaseCustomer;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_customer")
 */
#[ORM\Entity]
#[ORM\Table(name: 'sylius_customer')]
class Customer extends BaseCustomer
{
    /**
     * @var Collection<int, Wishlist>
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Wishlist::class)]
    private Collection $wishlists;

    public function __construct()
    {
        parent::__construct();
        $this->wishlists = new ArrayCollection();
    }

    /**
     * @return Collection<int, Wishlist>
     */
    public function getWishlists(): Collection
    {
        return $this->wishlists;
    }

    public function addWishlist(Wishlist $wishlist): static
    {
        if (!$this->wishlists->contains($wishlist)) {
            $this->wishlists->add($wishlist);
            $wishlist->setCustomer($this);
        }

        return $this;
    }

    public function removeWishlist(Wishlist $wishlist): static
    {
        if ($this->wishlists->removeElement($wishlist)) {
            // set the owning side to null (unless already changed)
            if ($wishlist->getCustomer() === $this) {
                $wishlist->setCustomer(null);
            }
        }

        return $this;
    }
}
