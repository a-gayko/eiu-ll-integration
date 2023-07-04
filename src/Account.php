<?php

declare(strict_types=1);

namespace EIU\LLIntegration;

class Account
{
    private Client $client;
    private Request $request;

    public function __construct($request)
    {
        $this->client = new Client();
        $this->request = $request;
    }

    public function createAccount(): string
    {
        $response = $this->client->apiPOST('@accounts', $this->accountData());

        return $response;
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