<?php

declare(strict_types=1);

namespace EIU\LLIntegration;


class Registration
{
    private Client $client;
    private Request $request;

    public function __construct($request)
    {
        $this->client = new Client();
        $this->request = $request;
    }

    public function createAccountIndividuals()
    {
        $response = $this->client->apiPOST('@account_individuals', $this->request->getRequestData());

        return $response;
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