<?php

declare(strict_types=1);

namespace EIU\LLIntegration;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Account
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

    public function createAccount(): Account|null
    {
        $payload = $this->accountData();
        $response = $this->client->apiPOST('@accounts', $payload);
        if (!isset($response->id)) {
            //failed
            $this->log->critical('Account request failed {payload}', ['payload' => $payload]);
            return null;
        }

        $account = new Account($response);
        $this->log->info(
            'Account request for account_name {account_name}  succeeded type={type} publisher_reference={publisher_reference} id={id}',
            [
                'type' => $account->type,
                'id' => $account->id,
                'account_name' => $account->account_name,
                'publisher_reference' => $account->publisher_reference,
            ]
        );

        return $account;
    }

    public function accountData(): string
    {
        $data = [
            "account_name" => $this->getUserName(),
            "enable_ip"=> true,
            "enable_pass_code"=> true,
            "enable_referrer"=> true,
            "enable_library_card"=> true,
            "enable_individual"=> true,
//            "metadata"=> [],
//            "enable_open_athens"=> true,
//            "enable_shibboleth"=> true,
//            "enable_archimed"=> true,
            "active"=> true,
//            "enable_self_registration"=> false,
//            "enable_lib_portal_stats"=> false,
//            "individual_limit"=> 0,
//            "enable_usage_control"=> false,
//            "individual"=> false,
            "publisher_reference"=> "eiu_store", //TO KNOW WHAT IS PUBLISH_REFERENCE
//            "enable_saml"=> false,
//            "enable_federated_saml"=> false,
            "type"=> "publisher",
        ];

        return json_encode($data);
    }

    private function getUserName(): string
    {
        $userName = $this->request->getRequestData()>individual->display_name;

        return json_encode($userName);
    }
}