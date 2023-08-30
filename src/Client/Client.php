<?php

declare(strict_types=1);

namespace EIU\LLIntegration\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use LogicException;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;
use RuntimeException;

/**
 * LibLynx Integration API client
 *
 * @package EIU\LLIntegration
 */
class Client implements LoggerAwareInterface
{
    private string $apiRoot = 'https://connect.liblynx.com';

    /** @var string client ID obtain from LibLynx Connect admin portal */
    private string $clientId;

    /** @var string client secret obtain from LibLynx Connect admin portal */
    private string $clientSecret;

    /** @var ClientInterface HTTP client for API requests */
    private ClientInterface $guzzle;

    /** @var \stdClass entry point resource */
    private \stdClass $entrypoint;

    /** @var CacheInterface */
    protected CacheInterface $cache;

    /** @var LoggerInterface */
    protected LoggerInterface $log;

    /** @var HTTPClientFactory */
    protected HTTPClientFactory $httpClientFactory;

    /**
     * Create new LibLynx Integration API client
     */
    public function __construct(HTTPClientFactory $clientFactory = null)
    {
        $this->clientId = defined(LIBLYNX_CLIENT_KEY) ? LIBLYNX_CLIENT_KEY : '';
        $this->clientSecret = defined(LIBLYNX_CLIENT_SECRET) ? LIBLYNX_CLIENT_SECRET : '';

        $this->log               = new NullLogger();
        $this->httpClientFactory = $clientFactory ?? new HTTPClientFactory();
    }

    /**
     * @inheritdoc
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->log = $logger;
    }

    /**
     * General purpose 'GET' request against API
     *
     * @param string $entrypoint contains either an @entrypoint or full URL
     *     obtained from a resource
     *
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function apiGET(string $entrypoint): array
    {
//        return $this->makeAPIRequest('GET', $entrypoint);
        $response = $this->makeAPIRequest('GET', $entrypoint);
        $contents = $response->getBody()->getContents();

        return json_decode($contents, true);
    }

    /**
     * General purpose 'POST' request against API
     *
     * @param string $entrypoint contains either an @entrypoint or full URL
     *     obtained from a resource
     * @param string|null $json contains JSON formatted data to post
     *
     * @return \stdClass|string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function apiPOST(string $entrypoint, ?string $json): StreamInterface
    {
        return $this->makeAPIRequest('POST', $entrypoint, $json);
    }

    /**
     * @param string $method
     * @param string $entrypoint
     * @param string|null $json
     *
     * @return \Psr\Http\Message\StreamInterface object containing JSON decoded response - note this
     *     can be an error response for normally handled errors
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function makeAPIRequest(
        string $method,
        string $entrypoint,
        ?string $json = null
    ): StreamInterface {
        $this->log->debug(
            '{method} {entry} {json}',
            ['method' => $method, 'entry' => $entrypoint, 'json' => $json]
        );
        $url    = $this->resolveEntryPoint($entrypoint);
        $client = $this->getClient();

        $headers = ['Accept' => 'application/json'];
        if (!empty($json)) {
            $headers['Content-Type'] = 'application/json';
        }

        $request = new Request($method, $url, $headers, $json);

        try {
            $response = $client->send($request);
            $this->log->debug(
                '{method} {entry} succeeded {status}',
                [
                    'method' => $method,
                    'entry'  => $entrypoint,
                    'status' => $response->getStatusCode(),
                ]
            );
        } catch (RequestException $e) {
            //we usually have a response available, but it's not guaranteed
            $response = $e->getResponse();
            $this->log->error(
                '{method} {entrypoint} {json} failed ({status}): {body}',
                [
                    'method'     => $method,
                    'json'       => $json,
                    'entrypoint' => $entrypoint,
                    'status'     => $response ? $response->getStatusCode() : 0,
                    'body'       => $response ? $response->getBody() : '',
                ]
            );

            throw new RuntimeException(
                "$method $entrypoint request failed",
                $e->getCode(),
                $e
            );
        } catch (GuzzleException $e) {
            $this->log->critical(
                '{method} {entry} {json} failed',
                ['method' => $method, 'json' => $json, 'entry' => $entrypoint]
            );

            throw new RuntimeException("$method $entrypoint failed", 0, $e);
        }

        return $response->getBody();
    }

    /**
     * @param $nameOrUrl
     *
     * @return string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function resolveEntryPoint($nameOrUrl): string
    {
        if ($nameOrUrl[0] === '@') {
            $resolved = $this->getEntryPoint($nameOrUrl);
            $this->log->debug(
                'Entrypoint {entrypoint} resolves to {url}',
                ['entrypoint' => $nameOrUrl, 'url' => $resolved]
            );

            return $resolved;
        }

        return $nameOrUrl;
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getEntryPoint($name): ?\stdClass
    {
        if (!is_array($this->entrypoint)) {
            $this->entrypoint = $this->getEntrypointResource();
        } else {
            $this->log->debug('using previously loaded entrypoint');
        }

        if (!isset($this->entrypoint->_links->$name->href)) {
            throw new LogicException("Invalid LibLynx Integration API entrypoint $name requested");
        }

        return $this->entrypoint->_links->$name->href;
    }

    /**
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getEntrypointResource(): array
    {
        $key = 'entrypoint' . $this->clientId;

        $cache = $this->getCache();
        if ($cache->has($key)) {
            $this->log->debug('loading entrypoint from persistent cache');
            $entrypointResource = $cache->get($key);
        } else {
            $this->log->debug('entrypoint not cached, requesting from API');
            $url = $this->apiRoot . '/api';
            $entrypointResource = $this->apiGET($url);
            $cache->set($key, $entrypointResource, 86400);
            $this->log->info('entrypoint loaded from API and cached');
        }

        return $entrypointResource;
    }

    /**
     * @return \Psr\SimpleCache\CacheInterface
     */
    public function getCache(): CacheInterface
    {
        return $this->cache;
    }

    /**
     * @param \Psr\SimpleCache\CacheInterface $cache
     *
     * @return void
     */
    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * Internal helper to provide an OAuth2 capable HTTP client
     */
    public function getClient(): ClientInterface
    {
        if (!is_object($this->guzzle)) {
            $this->guzzle = $this->httpClientFactory->create(
                $this->apiRoot,
                $this->clientId,
                $this->clientSecret,
                $this->getCache()
            );
        }

        return $this->guzzle;
    }
}
