<?php

declare(strict_types=1);

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;
use EIU\LLIntegration\Resource\Subscription;

class SubscriptionRequest extends AbstractApiRequest
{
    /**
     * @return string
     */
    public function getRequestDataJSON(): string
    {
        $data = [
            'title' => $_POST['llsub_title'],
            'package_code' => $_POST['llsub_package_code'],
            'trial' => false,
            'perpetual' => true,
            'start' => $_POST['llsub_start'],
            'end' => $_POST['llsub_end'],
        ];

        return json_encode($data);
    }

    /**
     * @return string
     */
    protected function getApiEndpoint(): string
    {
        return '@account_subs';
    }

    /**
     * @return string
     */
    protected function getLogMessage(): string
    {
        return 'Subscription request failed {payload}';
    }

    /**
     * @param $response
     * @return ApiResourceInterface
     */
    protected function createResource($response): ApiResourceInterface
    {
        return new Subscription($response);
    }

    /**
     * @return string
     */
    protected function getSuccessLogMessage(): string
    {
        return 'Subscription request for id {id} succeeded';
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
