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
 */
class Account extends AbstractApiResource
{
    public function isIdentified(): bool
    {
        return $this->status == 'identified';
    }

    public function requiresWayf(): bool
    {
        return $this->status == 'wayf';
    }

    public function getWayfUrl(): string
    {
        return $this->getLink('wayf');
    }
}
