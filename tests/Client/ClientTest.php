<?php

declare(strict_types=1);

namespace EIU\LLIntegration;

use EIU\LLIntegration\Client\Client;
use EIU\LLIntegration\Client\HTTPClientFactory;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Log\NullLogger;

/**
 * PHPUnit test class for Client.
 *
 * @package EIU\LLIntegration\Client
 */
class ClientTest extends TestCase
{
    /**
     * This method is called before each test.
     *
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        $httpClientFactory = $this->createMock(HTTPClientFactory::class);
        $httpClientFactory->method('create')
                          ->willReturn($this->createMock(ClientInterface::class));
        $log = $this->createMock(NullLogger::class);

        $this->client = $this->getMockBuilder(Client::class)
                             ->setConstructorArgs([
                                 '123',
                                 '456',
                                 $httpClientFactory,
                                 $log,
                             ])
                             ->getMock();
    }

    /**
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testApiGET()
    {
        $entrypoint = '@some_entrypoint';
        $response   = $this->client->apiGET($entrypoint);

        // Assertions
        static::assertIsArray($response);
    }

    /**
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testApiPOST()
    {
        $entrypoint = '@some_entrypoint';
        $json       = json_encode(['data' => 'test']);
        $response   = $this->client->apiPOST($entrypoint, $json);

        // Assertions
        static::assertInstanceOf(StreamInterface::class, $response);
    }

    /**
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testResolveEntryPoint()
    {
        $entrypoint = '@some_entrypoint';
        $resolved   = $this->client->resolveEntryPoint($entrypoint);

        // Assertions
        static::assertIsString($resolved);
    }

    /**
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testGetEntrypointResource()
    {
        $entrypointResource = $this->client->getEntrypointResource();

        // Assertions
        static::assertIsArray($entrypointResource);
    }

    /**
     * @return void
     */
    public function testGetClient()
    {
        $returnedClient = $this->client->getClient();

        // Assertions
        static::assertInstanceOf(ClientInterface::class, $returnedClient);
    }
}
