<?php

declare(strict_types=1);

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Resource\Account;
use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;

/**
 * Class AccountRequest
 * @package EIU\LLIntegration\RequestResource
 */
class AccountRequest extends AbstractApiRequest
{
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    protected function getApiEndpoint(): string
    {
        return '@accounts';
    }

    /**
     * {@inheritdoc}
     */
    protected function getLogMessage(): string
    {
        return 'Account request failed {payload}';
    }

    /**
     * {@inheritdoc}
     */
    protected function createResource(mixed $response): ApiResourceInterface
    {
        return new Account($response);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSuccessLogMessage(): string
    {
        return 'Account request for account_name {account_name} succeeded id={id}';
    }

    /**
     * {@inheritdoc}
     */
    protected function getSuccessLogContext(ApiResourceInterface $resource): array
    {
        return [
            'id'           => $resource->id,
            'account_name' => $resource->account_name,
        ];
    }
}
