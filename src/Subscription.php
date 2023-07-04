<?php

declare(strict_types=1);

namespace EIU\LLIntegration;

class Subscription
{
    private Client $client;
    private Request $request;
    public function __construct($request)
    {
        $this->client = new Client();
        $this->request = $request;
    }
    public function createSubscription(): string
    {
        $response = $this->client->apiPOST('@account_subs', $this->request->getRequestData());

        return $response;
    }
}