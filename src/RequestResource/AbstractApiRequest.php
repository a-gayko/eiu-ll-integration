<?php

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Client\Client;
use EIU\LLIntegration\Interface\ApiRequestInterface;
use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 *
 */
abstract class AbstractApiRequest implements ApiRequestInterface
{
    protected Client $client;
    protected LoggerInterface $log;


    protected function __construct()
    {
        $this->client = new Client();
        $this->log = new NullLogger();
    }

    /**
     * @return string
     */
    abstract public function getRequestDataJSON(): string;

    /**
     * @return ApiRequestInterface|null
     */
    public function sendRequest(): ?ApiResourceInterface
    {
        $payload = $this->getRequestDataJSON();
        $response = $this->client->apiPOST($this->getApiEndpoint(), $payload);
        if (!isset($response->id)) {
            $this->log->critical($this->getLogMessage(), ['payload' => $payload]);

            return null;
        }

        $resource = $this->createResource($response);
        $this->log->info($this->getSuccessLogMessage(), $this->getSuccessLogContext($resource));

        return $resource;
    }

    /**
     * @return string
     */
    abstract protected function getApiEndpoint(): string;

    /**
     * @return string
     */
    abstract protected function getLogMessage(): string;

    /**
     * @param $response
     * @return ApiResourceInterface
     */
    abstract protected function createResource($response): ApiResourceInterface;

    /**
     * @return string
     */
    abstract protected function getSuccessLogMessage(): string;

    /**
     * @param ApiResourceInterface $resource
     * @return array
     */
    abstract protected function getSuccessLogContext(ApiResourceInterface $resource): array;
}
