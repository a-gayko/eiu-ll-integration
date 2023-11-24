<?php

declare(strict_types=1);

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Resource\AbstractApiResource;
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
    public static function getRequestDataJSON(): string
    {
        $target_url = $_REQUEST['_wp_http_referer'] ?? $_SERVER['REQUEST_URI'];
        $userData   = [
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

        return json_encode($userData);
    }

    /**
     * {@inheritdoc}
     */
    public function getApiEndpoint(): string
    {
        return '@account_individuals';
    }

    /**
     * {@inheritdoc}
     */
    public function createResource(mixed $response): AbstractApiResource
    {
        return new Registration($response);
    }

    /**
     * {@inheritdoc}
     */
    public function getFailLogMessage(): string
    {
        return 'Registration request failed {payload}';
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessLogMessage(): string
    {
        return 'Registration request for display_name {display_name} succeeded id={id} type={type}';
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessLogContext(AbstractApiResource $resource): array
    {
        return [
            'id'         => $resource->id,
            'type'       => $resource->type,
            'individual' => $resource->individual->display_name,
        ];
    }
}
