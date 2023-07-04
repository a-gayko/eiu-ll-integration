<?php

declare(strict_types=1);

namespace EIU\LLIntegration;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Registration
{
    private Client $client;
    private NullLogger $log;
    private Request $request;

    public function __construct($request)
    {
        $this->client = new Client();
        $this->log = new NullLogger();
        $this->request = $request;
    }

    public function createAccountIndividuals(): Registration|null
    {
        $payload = $this->request->getRequestData();
        $response = $this->client->apiPOST('@account_individuals', $payload);
        if (!isset($response->id)) {
            //failed
            $this->log->critical('Registration request failed {payload}', ['payload' => $payload]);
            return null;
        }

        $registration = new Registration($response);
        $this->log->info(
            'Registration request for display_name {display_name}  succeeded type={type} id={id}',
            [
                'type' => $registration->type,
                'id' => $registration->id,
                'display_name' => $registration->individual->display_name,
            ]
        );

        return $registration;
    }

//    private function getAccountId(): mixed
//    {
//        $accountId = null;
//
//        if($this->createAccount() !== null) {
//            $accountId = json_decode($this->createAccount())[0];
//        }
//
//        return $accountId;
//    }
}