<?php

declare(strict_types=1);

namespace EIU\LLIntegration\Interface;

use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;

/**
 *
 */
interface ApiRequestInterface
{
    /**
     * @return string
     */
    public function getRequestDataJSON(): string;

    /**
     * @return ApiRequestInterface|null
     */
    public function sendRequest(): ?ApiResourceInterface;
}
