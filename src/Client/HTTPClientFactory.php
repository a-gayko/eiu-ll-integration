<?php

declare(strict_types=1);

namespace EIU\LLIntegration\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as GuzzleClient;
use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\OAuth2Middleware;
use kamermans\OAuth2\Persistence\SimpleCacheTokenPersistence;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\Psr16CacheStorage;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Psr\SimpleCache\CacheInterface;

/**
 * Provides the means for creating an OAuth2-capable, cache-aware HTTP client
 *
 * This can also be modified to provide the means to test the client
 *
 * @package EIU\LLIntegration
 */
class HTTPClientFactory
{
    /** @var CacheInterface */
    protected CacheInterface $cache;

    /** @var callable RequestStack handler for API requests */
    private $apiHandler;

    /** @var callable RequestStack handler for OAuth2 requests */
    private $oAuth2Handler;

    public function __construct(
        callable $apiHandler = null,
        callable $oAuth2Handler = null
    ) {
        $this->apiHandler    = $apiHandler;
        $this->oAuth2Handler = $oAuth2Handler;
    }

    /**
     * @param $apiRoot
     * @param $clientId
     * @param $clientSecret
     * @param \Psr\SimpleCache\CacheInterface $cache
     *
     * @return \GuzzleHttp\ClientInterface
     */
    public function create(
        $apiRoot,
        $clientId,
        $clientSecret,
        CacheInterface $cache
    ): ClientInterface {
        //create our handler stack (which may be mocked in tests) and add the oauth and cache middleware
        $handlerStack = HandlerStack::create($this->apiHandler);
        $handlerStack->push($this->createOAuth2Middleware(
            $apiRoot,
            $clientId,
            $clientSecret,
            $cache
        ));
        $handlerStack->push($this->createCacheMiddleware($cache), 'cache');

        //now we can make our client
        $client = new GuzzleClient([
            'handler'  => $handlerStack,
            'auth'     => 'oauth',
            'base_uri' => $apiRoot,
        ]);

        return $client;
    }

    /**
     * @param $apiRoot
     * @param $id
     * @param $secret
     * @param \Psr\SimpleCache\CacheInterface $cache
     *
     * @return \kamermans\OAuth2\OAuth2Middleware
     */
    protected function createOAuth2Middleware(
        $apiRoot,
        $id,
        $secret,
        CacheInterface $cache
    ): OAuth2Middleware {
        $handlerStack = HandlerStack::create($this->oAuth2Handler);

        // Authorization client - this is used to request OAuth access tokens
        $reauth_client = new GuzzleClient([
            'handler'  => $handlerStack,
            // URL for access_token request
            'base_uri' => $apiRoot . '/oauth/v2/token',
        ]);
        $reauth_config = [
            'client_id'     => $id,
            'client_secret' => $secret,
        ];
        $grant_type    = new ClientCredentials($reauth_client, $reauth_config);
        $oauth         = new OAuth2Middleware($grant_type);

        //use our cache to store tokens
        $oauth->setTokenPersistence(new SimpleCacheTokenPersistence($cache));

        return $oauth;
    }

    /**
     * @param \Psr\SimpleCache\CacheInterface $cache
     *
     * @return \Kevinrob\GuzzleCache\CacheMiddleware
     */
    protected function createCacheMiddleware(CacheInterface $cache): CacheMiddleware
    {
        return new CacheMiddleware(
            new PrivateCacheStrategy(
                new Psr16CacheStorage($cache)
            )
        );
    }
}
