<?php

declare(strict_types=1);

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Resource\Account;
use EIU\LLIntegration\Resource\AbstractApiResource;
use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;

/**
 * Class AccountRequest
 *
 * @package EIU\LLIntegration\RequestResource
 */
class AccountRequest extends AbstractApiRequest
{
    /**
     * {@inheritdoc}
     */
    public static function getRequestDataJSON(): string
    {
        $userData = [
            'account_name'        => $_POST['llacc_name'],
            'publisher_reference' => '',
            'enable_ip'           => true,
            'enable_pass_code'    => true,
            'enable_referrer'     => true,
            'enable_individual'   => true,
        ];

        return json_encode($userData);
    }

    /**
     * {@inheritdoc}
     */
    public function getApiEndpoint(): string
    {
        return '@accounts';
    }

    /**
     * {@inheritdoc}
     */
    public function createResource(mixed $response): AbstractApiResource
    {
        return new Account($response);
    }

    /**
     * {@inheritdoc}
     */
    public function getFailLogMessage(): string
    {
        return 'Account request failed {payload}';
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessLogMessage(): string
    {
        return 'Account request for account_name {account_name} succeeded id={id}';
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessLogContext(AbstractApiResource $resource): array
    {
        return [
            'id'           => $resource->id,
            'account_name' => $resource->account_name,
            'active' => $resource->active,
        ];
    }
}
