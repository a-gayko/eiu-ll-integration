<?php

namespace EIU\LLIntegration\Resource\Interface;

use stdClass;

/**
 * Interface ApiResourceInterface
 * Represents an API resource.
 *
 * @package EIU\LLIntegration\Resource\Interface
 */
interface ApiResourceInterface
{
    /**
     * Get the status of the API resource.
     *
     * @return stdClass Status of the API resource.
     */
    public function getStatus(): stdClass;
}
