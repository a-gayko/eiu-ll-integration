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
     * Send the API request and process the response.
     *
     * @return ApiResourceInterface|null API resource or null on failure.
     */
    public function sendRequest(): ?ApiResourceInterface;
}
