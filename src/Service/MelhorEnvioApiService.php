<?php
// src/Service/MelhorEnvioApiService.php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class MelhorEnvioApiService
{
    private Client $client;
    private string $apiUrl;
    private string $accessToken;

    public function __construct(string $accessToken)
    {
        $this->client = new Client();
        $this->apiUrl = 'https://melhorenvio.com.br/api/v2/me/shipment/calculate';
        $this->accessToken = $accessToken;
    }

    public function calculateShipment(array $data): ?array
    {
        try {
            $response = $this->client->post($this->apiUrl, [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            return $responseData;
        } catch (RequestException $e) {
            // Gérer les erreurs de l'API
            return null;
        }
    }
}
