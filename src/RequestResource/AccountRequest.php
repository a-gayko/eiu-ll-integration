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
     * @return string
     */
    protected function getApiEndpoint(): string
    {
        return '@accounts';
    }

    /**
     * @return string
     */
    protected function getLogMessage(): string
    {
        return 'Account request failed {payload}';
    }

    /**
     * @param $response
     * @return ApiResourceInterface
     */
    protected function createResource($response): ApiResourceInterface
    {
        return new Account($response);
    }

    /**
     * @return string
     */
    protected function getSuccessLogMessage(): string
    {
        return 'Account request for account_name {account_name} succeeded id={id}';
    }

    /**
     * @param ApiResourceInterface $resource
     * @return array
     */
    protected function getSuccessLogContext(ApiResourceInterface $resource): array
    {
        return [
            'id'           => $resource->id,
            'account_name' => $resource->account_name,
        ];
    }

}
