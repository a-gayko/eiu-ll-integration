<?php

declare(strict_types=1);

namespace EIU\LLIntegration\Resource;

use stdClass;

/**
 * Provides a simple wrapper around a LibLynx identification resource
 *
 * @package EIU\LLIntegration
 *
 * @property stdClass $id
 * @property stdClass $type
 * @property stdClass $individual
 */
class Registration extends AbstractApiResource
{
    /**
     * Get the status of the subscription.
     *
     * @return bool|string Status of the subscription.
     */
    public function getStatus(): bool | string
    {
        // TODO: Implement getStatus() method.
    }
}
