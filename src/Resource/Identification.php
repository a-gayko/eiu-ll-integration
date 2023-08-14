<?php

declare(strict_types=1);

namespace EIU\LLIntegration\Resource;

use stdClass;

/**
 * Provides a simple wrapper around a LibLynx identification resource
 * @package EIU\LLIntegration
 *
 * @property stdClass $id
 * @property stdClass $ip
 * @property stdClass $url
 * @property stdClass $status
 */
class Identification extends AbstractApiResource
{
    /**
     * @return bool
     */
    public function isIdentified(): bool
    {
        return $this->status == 'identified';
    }

    /**
     * @return bool
     */
    public function requiresWayf(): bool
    {
        return $this->status == 'wayf';
    }

    /**
     * @return string
     */
    public function getWayfUrl(): string
    {
        return $this->getLink('wayf');
    }
}
