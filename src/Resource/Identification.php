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
 * @property stdClass $ip
 * @property stdClass $url
 * @property stdClass $status
 * @property stdClass $_links
 */
class Identification extends AbstractApiResource
{
    /**
     * Get the status of the identification.
     *
     * @return bool|string Status of the identification.
     */
    public function getStatus(): bool | string
    {
        return match ($this->status) {
            'identified' => 'identified',
            'wayf' => 'wayf',
            default => 'error',
        };
    }

    public function getWayfUrl(): string
    {
        return $this->getLink('wayf');
    }

    public function getLink(string $linkName): string | null
    {
        if (!empty($_links)) {
            return $_links->$linkName->href;
        }

        return null;
    }
}
