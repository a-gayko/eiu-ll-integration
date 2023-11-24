<?php

declare(strict_types=1);

namespace Client;

use EIU\LLIntegration\Client\HTTPClientFactory;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use kamermans\OAuth2\OAuth2Middleware;
use Psr\SimpleCache\CacheInterface;
use PHPUnit\Framework\TestCase;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Kevinrob\GuzzleCache\Storage\Psr16CacheStorage;

/**
 * PHPUnit test class for HTTPClientFactory.
 *
 * @package EIU\LLIntegration\Client
 */
class HTTPClientFactoryTest extends TestCase
{
    /**
     * This method is called before each test.
     *
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        $this->cache = $this->createMock(CacheInterface::class);
    }
    /**
     * @return void
     */
    public function testCreate()
    {
        $apiHandler = function () {
            return new Response(200, [], 'API Response');
        };
        $factory = new HTTPClientFactory($apiHandler);
        $client = $factory->create('http://example.com', 'clientId', 'clientSecret', $this->cache);

        // Assertions
        $this->assertInstanceOf(ClientInterface::class, $client);
    }

    /**
     * @return void
     */
    public function testCreateOAuth2Middleware()
    {
        $oAuth2Handler = function () {
            return new Response(200, [], 'OAuth2 Response');
        };
        $factory = new HTTPClientFactory(null, $oAuth2Handler);
        $middleware = $factory->createOAuth2Middleware('http://example.com', 'clientId', 'clientSecret', $this->cache);

        // Assertions
        $this->assertInstanceOf(OAuth2Middleware::class, $middleware);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testCreateCacheMiddleware()
    {
        $factory = new HTTPClientFactory();
        $middleware = $factory->createCacheMiddleware($this->cache);

        // Assertions
        $this->assertInstanceOf(CacheMiddleware::class, $middleware);
    }
}
