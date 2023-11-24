<?php

declare(strict_types=1);

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Resource\Identification;
use EIU\LLIntegration\Resource\AbstractApiResource;

/**
 * Class IdentificationRequest
 *
 * @package EIU\LLIntegration\RequestResource
 */
class IdentificationRequest extends AbstractApiRequest
{
    /**
     * {@inheritdoc}
     */
    public static function getRequestDataJSON(): string
    {
        $data = [
            'ip'            => $_SERVER['REMOTE_ADDR'] ?? null,
            'referrer'      => $_SERVER['HTTP_REFERER'] ?? null,
            'user_agent'    => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'url'           => $_SERVER['HTTPS'] . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?? null,
            'unit_requests' => [],
        ];

        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getApiEndpoint(): string
    {
        return '@new_identification';
    }

    /**
     * {@inheritdoc}
     */
    public function createResource(mixed $response): AbstractApiResource
    {
        return new Identification($response);
    }

    /**
     * {@inheritdoc}
     */
    public function getFailLogMessage(): string
    {
        return 'Identification request failed {payload}';
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessLogMessage(): string
    {
        return 'Identification request for ip {ip} on URL {url} succeeded status={status} id={id}';
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessLogContext(AbstractApiResource $resource): array
    {
        return [
            'status' => $resource->status,
            'id'     => $resource->id,
            'ip'     => $resource->ip,
            'url'    => $resource->url,
        ];
    }
}
