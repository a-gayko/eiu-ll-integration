<?php

declare(strict_types=1);

namespace EIU\LLIntegration\Resource;

use EIU\LLIntegration\RequestResource\IdentificationRequest;
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
        $status = '';
        switch ($this->status) {
            case 'identified':
                $status = 'identified';
                break;
            case 'wayf':
                $status = 'wayf';
                break;
        }

        return $status;
    }
}
