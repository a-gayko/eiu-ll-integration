<?php

declare(strict_types=1);

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;
use EIU\LLIntegration\Resource\Registration;

/**
 *
 */
class RegistrationRequest extends AbstractApiRequest
{
    /**
     * @return string
     */
    public function getRequestDataJSON(): string
    {
        $target_url = $_SERVER['HTTPS'] . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?? null;
        $data = [
            'type' => 'user',
            'individual' => [
                'display_name' => $_POST['llacc_name'],
                'password'     => $_POST['llacc_password'],
                'email'        => [
                    $_POST['ll_email'],
                ],
            ],
            'email' => false,
            'target_url' => $target_url,
        ];

        return json_encode($data);
    }

    /**
     * @return string
     */
    protected function getApiEndpoint(): string
    {
        return '@account_individuals';
    }

    /**
     * @return string
     */
    protected function getLogMessage(): string
    {
        return 'Registration request failed {payload}';
    }

    /**
     * @param $response
     * @return ApiResourceInterface
     */
    protected function createResource($response): ApiResourceInterface
    {
        return new Registration($response);
    }

    /**
     * @return string
     */
    protected function getSuccessLogMessage(): string
    {
        return 'Registration request for id {id} succeeded';
    }

    /**
     * @param ApiResourceInterface $resource
     * @return array
     */
    protected function getSuccessLogContext(ApiResourceInterface $resource): array
    {
        return [
            'id' => $resource->id,
        ];
    }
}
