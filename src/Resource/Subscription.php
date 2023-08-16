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
 * @property stdClass $title
 * @property stdClass $package_code
 * @property stdClass $active
 */
class Subscription extends AbstractApiResource
{
    /**
     * Get the status of the subscription.
     *
     * @return bool|string Status of the subscription.
     */
    public function getStatus(): bool | string
    {
        return $this->active == true;
    }
}
