<?php

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Client\Client;
use EIU\LLIntegration\Interface\ApiRequestInterface;
use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Abstract base class for API requests.
 *
 * @package EIU\LLIntegration\RequestResource
 */
abstract class AbstractApiRequest implements ApiRequestInterface
{
    /**
     * Client for making API requests.
     * @var Client
     */
    protected Client $client;

    /**
     * Logger for logging messages.
     * @var LoggerInterface
     */
    protected LoggerInterface $log;

    /**
     * AbstractApiRequest constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
        $this->log = new NullLogger();
    }

    /**
     * @return string
     */
    abstract public function getRequestDataJSON(): string;

    /**
     * Get the API endpoint for the request.
     *
     * @return string API endpoint.
     */
    abstract protected function getApiEndpoint(): string;

    /**
     * Get the log message for a failed request.
     *
     * @return string Log message.
     */
    abstract protected function getLogMessage(): string;

    /**
     * Create an API resource from the response data.
     *
     * @param mixed $response Response data.
     * @return ApiResourceInterface Created API resource.
     */
    abstract protected function createResource(mixed $response): ApiResourceInterface;

    /**
     * Get the log message for a successful request.
     *
     * @return string Log message.
     */
    abstract protected function getSuccessLogMessage(): string;

    /**
     * Get the log context for a successful request.
     *
     * @param ApiResourceInterface $resource Created API resource.
     * @return array Log context.
     */
    abstract protected function getSuccessLogContext(ApiResourceInterface $resource): array;

    /**
     * {@inheritdoc}
     */
    public function sendRequest(): ?ApiResourceInterface
    {
        try {
            $payload = $this->getRequestDataJSON();
            $response = $this->client->apiPOST($this->getApiEndpoint(), $payload);

            if (!isset($response->id)) {
                $this->log->critical($this->getLogMessage(), ['payload' => $payload]);
                return null;
            }

            $resource = $this->createResource($response);
            $this->log->info($this->getSuccessLogMessage(), $this->getSuccessLogContext($resource));

            return $resource;
        } catch (\Exception $e) {
            $this->log->error('Request failed with exception: ' . $e->getMessage());
            return null;
        }
    }
}
