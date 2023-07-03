<?php

declare(strict_types=1);

namespace EIU\LLIntegration\Registration;

use EIU\LLIntegration\Account;
use EIU\LLIntegration\Client;
use EIU\LLIntegration\Registration\RegistrationRequest as Request;

class Registration
{
    private Account $account;
    private Client $client;
    private Request $requestFromRegisterForm;

    public function __construct($account, $client, $requestFromRegisterForm)
    {
        $this->account = $account;
        $this->client = $client;
        $this->requestFromRegisterForm = $requestFromRegisterForm;
    }

    public function createAccount(): string
    {
        $response = $this->client->apiPOST('@accounts', $this->account->setAccountData());

        return $response;
    }

    public function createAccountIndividuals()
    {
        $response = $this->client->apiPOST('@account_individuals', $this->requestFromRegisterForm->getRequestData());

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