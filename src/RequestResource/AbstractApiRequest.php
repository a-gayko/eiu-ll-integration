<?php

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\RequestResource\Interface\ApiRequestInterface;
use EIU\LLIntegration\Resource\AbstractApiResource;
use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;
use Psr\Log\NullLogger;

/**
 * Abstract base class for API requests.
 *
 * @package EIU\LLIntegration\RequestResource
 */
abstract class AbstractApiRequest implements ApiRequestInterface
{
    /**
     * AbstractApiRequest constructor.
     *
     * @param \Psr\Log\NullLogger $log
     */
    public function __construct(
        NullLogger $log = new NullLogger()
    ) {
    }

    /**
     * Get the log message for a failed request.
     *
     * @return string Log message.
     */
    abstract public function getFailLogMessage(): string;

    /**
     * Get the log message for a successful request.
     *
     * @return string Log message.
     */
    abstract public function getSuccessLogMessage(): string;

    /**
     * Get the log context for a successful request.
     *
     * @param ApiResourceInterface $resource Created API resource.
     *
     * @return array Log context.
     */
    abstract public function getSuccessLogContext(AbstractApiResource $resource): array;
}
