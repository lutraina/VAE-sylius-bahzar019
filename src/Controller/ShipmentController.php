<?php

// Exemple d'appel dans un contrôleur
namespace App\Controller;

use App\Service\MelhorEnvioApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\RedirectResponse;
class ShipmentController extends AbstractController
{
    private MelhorEnvioApiService $melhorEnvioApiService;

    public function __construct(MelhorEnvioApiService $melhorEnvioApiService)
    {
        $this->melhorEnvioApiService = $melhorEnvioApiService;
    }

    /**
     * @Route("/calculate_ship", name="calculate_ship")
     */
    public function calculate(): Response
    {
        $requestData = [
            "from" => [
                "postal_code" => "13015-301"
            ],
            "to" => [
                "postal_code" => "13026-540"
            ],
            "products" => [
                [
                    "id" => "x",
                    "width" => 11,
                    "height" => 17,
                    "length" => 11,
                    "weight" => 0.3,
                    "insurance_value" => 10.1,
                    "quantity" => 1
                ]
            ],
            "options" => [
                "receipt" => false,
                "own_hand" => false
            ],
            "services" => "1,2,18"
        ];

        $response = $this->melhorEnvioApiService->calculateShipment($requestData);

        if ($response) {
            return $this->json($response);
        }

        return $this->json(['error' => 'Unable to calculate shipment'], Response::HTTP_BAD_REQUEST);
    }
}

