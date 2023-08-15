<?php

declare(strict_types=1);

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Resource\Identification;
use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;

/**
 * Class IdentificationRequest
 * @package EIU\LLIntegration\RequestResource
 */
class IdentificationRequest extends AbstractApiRequest
{
    /**
     * {@inheritdoc}
     */
    public function getRequestDataJSON(): string
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
    protected function getApiEndpoint(): string
    {
        return '@new_identification';
    }

    /**
     * {@inheritdoc}
     */
    protected function getLogMessage(): string
    {
        return 'Identification request failed {payload}';
    }

    /**
     * {@inheritdoc}
     */
    protected function createResource(mixed $response): ApiResourceInterface
    {
        return new Identification($response);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSuccessLogMessage(): string
    {
        return 'Identification request for ip {ip} on URL {url} succeeded status={status} id={id}';
    }

    /**
     * {@inheritdoc}
     */
    protected function getSuccessLogContext(ApiResourceInterface $resource): array
    {
        return [
            'status' => $resource->status,
            'id'     => $resource->id,
            'ip'     => $resource->ip,
            'url'    => $resource->url,
        ];
    }
}
