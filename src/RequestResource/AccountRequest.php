<?php

declare(strict_types=1);

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Interface\ApiRequestInterface;
use EIU\LLIntegration\Resource\Account;
use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;

/**
 *
 */
class AccountRequest extends AbstractApiRequest
{
    /**
     * @return string
     */
    public function getRequestDataJSON(): string
    {
        $data = [
            'account_name'      => $_POST['llacc_name'],
            'enable_ip'         => true,
            'enable_pass_code'  => true,
            'enable_referrer'   => true,
            'enable_individual' => true,
        ];

        return json_encode($data);
    }

    /**
     * @return ApiRequestInterface|null
     */
    public function sendRequest(): ?ApiResourceInterface
    {
        $payload = $this->getRequestDataJSON();
        $response = $this->client->apiPOST('@accounts', $payload);
        if (!isset($response->id)) {
            $this->log->critical('Account request failed {payload}', ['payload' => $payload]);

            return null;
        }

        $account = new Account($response);
        $this->log->info(
            'Account request for account_name {account_name} succeeded id={id}',
            [
                'id'           => $account->id,
                'account_name' => $account->account_name,
            ]
        );

        return $account;
    }
}
