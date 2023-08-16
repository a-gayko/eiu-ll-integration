<?php

declare(strict_types=1);

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;
use EIU\LLIntegration\Resource\Registration;

/**
 * Class RegistrationRequest
 *
 * @package EIU\LLIntegration\RequestResource
 */
class RegistrationRequest extends AbstractApiRequest
{
    /**
     * {@inheritdoc}
     */
    public function getRequestDataJSON(): string
    {
        $target_url = $_SERVER['HTTPS'] . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?? null;
        $data       = [
            'type'       => 'user',
            'individual' => [
                'display_name' => $_POST['llacc_name'],
                'password'     => $_POST['llacc_password'],
                'email'        => [
                    $_POST['ll_email'],
                ],
            ],
            'email'      => false,
            'target_url' => $target_url,
        ];

        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    protected function getApiEndpoint(): string
    {
        return '@account_individuals';
    }

    /**
     * {@inheritdoc}
     */
    protected function getLogMessage(): string
    {
        return 'Registration request failed {payload}';
    }

    /**
     * {@inheritdoc}
     */
    protected function createResource(mixed $response): ApiResourceInterface
    {
        return new Registration($response);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSuccessLogMessage(): string
    {
        return 'Registration request for display_name {display_name} succeeded id={id} type={type}';
    }

    /**
     * {@inheritdoc}
     */
    protected function getSuccessLogContext(ApiResourceInterface $resource): array
    {
        return [
            'id'         => $resource->id,
            'type'       => $resource->type,
            'individual' => $resource->individual->display_name,
        ];
    }
}
