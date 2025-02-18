<?php

namespace App\Tests\Service;

use App\Service\MelhorEnvioApiService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Mockery;

class MelhorEnvioApiServiceTest extends TestCase
{
    public function testCalculateShipmentSuccess()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockResponse = new Response(200, [], json_encode(['success' => true, 'price' => 50]));

        $mockClient->shouldReceive('post')
            ->once()
            ->andReturn($mockResponse);

        $service = new MelhorEnvioApiService('fake_access_token');

        // Injection du client mocké
        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($service, $mockClient);

        $data = ['from' => '10000', 'to' => '20000', 'weight' => 2];
        $result = $service->calculateShipment($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }

    public function testCalculateShipmentFailure()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('post')
            ->once()
            ->andThrow(new RequestException('Error Communicating with Server', new Request('POST', 'test')));

        $service = new MelhorEnvioApiService('fake_access_token');

        // Injection du client mocké
        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($service, $mockClient);

        $data = ['from' => '10000', 'to' => '20000', 'weight' => 2];
        $result = $service->calculateShipment($data);

        $this->assertNull($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
