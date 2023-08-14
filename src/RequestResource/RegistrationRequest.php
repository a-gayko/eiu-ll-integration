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
     * @return ApiResourceInterface|null
     */
    public function sendRequest(): ?ApiResourceInterface
    {
        $payload = $this->getRequestDataJSON();
        $response = $this->client->apiPOST('@account_individuals', $payload);
        if (!isset($response->id)) {
            //failed
            $this->log->critical('Registration request failed {payload}', ['payload' => $payload]);

            return null;
        }

        $registration = new Registration($response);
        $this->log->info(
            'Registration request for id {id}  succeeded',
            [
                'id' => $registration->id,
            ]
        );

        return $registration;
    }
}
