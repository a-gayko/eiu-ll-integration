<?php

declare(strict_types=1);

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;
use EIU\LLIntegration\Resource\Subscription;

/**
 *
 */
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
     * @return ApiResourceInterface|null
     */
    public function sendRequest(): ?ApiResourceInterface
    {
        $payload = $this->getRequestDataJSON();
        $response = $this->client->apiPOST('@account_subs', $payload);
        if (!isset($response->id)) {
            //failed
            $this->log->critical('Subscription request failed {payload}', ['payload' => $payload]);

            return null;
        }

        $subscription = new Subscription($response);
        $this->log->info(
            'Subscription request for id {id} succeeded',
            [
                'id' => $subscription->id,
            ]
        );

        return $subscription;
    }
}
