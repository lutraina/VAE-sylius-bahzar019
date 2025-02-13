Feature: Gestion des produits
    Scenario: Ajouter un produit dans le back-office
        Given I am logged in as an administrator
        When I create a new product named "T-Shirt Sylius"
        Then I should see the product "T-Shirt Sylius" in the admin panel
