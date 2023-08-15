<?php

declare(strict_types=1);

namespace EIU\LLIntegration\Resource;

use stdClass;

/**
 * Provides a simple wrapper around a LibLynx identification resource
 * @package EIU\LLIntegration
 *
 * @property stdClass $id
 * @property stdClass $account_name
 * @property stdClass $active
 */
class Account extends AbstractApiResource
{
    /**
     * Get the status of the subscription.
     *
     * @return stdClass Status of the subscription.
     */
    public function getStatus(): stdClass
    {
        return $this->active;
    }
}
