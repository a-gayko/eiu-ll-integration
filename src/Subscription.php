<?php

declare(strict_types=1);

namespace EIU\LLIntegration;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Subscription
{
    private Client $client;
    private LoggerInterface $log;
    private Request $request;
    public function __construct($request)
    {
        $this->client = new Client();
        $this->log = new NullLogger();
        $this->request = $request;
    }
    public function createSubscription(): Subscription|null
    {
        $payload = $this->request->getRequestData();
        $response = $this->client->apiPOST('@account_subs', $payload);
        if (!isset($response->id)) {
            //failed
            $this->log->critical('Subscription request failed {payload}', ['payload' => $payload]);
            return null;
        }

        $subscription = new Subscription($response);
        $this->log->info(
            'Subscription request for title {title} succeeded package_code={package_code} id={id} account_id={account_id}',
            [
                'package_code' => $subscription->package_code,
                'id' => $subscription->id,
                'account_id' => $subscription->account_id,
                'title' => $subscription->title,
            ]
        );
        return $subscription;
    }
}