<?php

declare(strict_types=1);

namespace EIU\LLIntegration\RequestResource\Interface;

use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;

/**
 * Interface for API requests.
 *
 * @package EIU\LLIntegration\RequestResource\Interface
 */
interface ApiRequestInterface
{
    /**
     * Get JSON representation of the request data.
     *
     * @return string JSON representation of the request data.
     */
    public function getRequestDataJSON(): string;

    /**
     * Get the API endpoint for the request.
     *
     * @return string API endpoint.
     */
    public function getApiEndpoint(): string;

    /**
     * Create an API resource from the response data.
     *
     * @param mixed $response Response data.
     *
     * @return ApiResourceInterface Created API resource.
     */
    public function createResource(mixed $response): ApiResourceInterface;
}
