<?php

declare(strict_types=1);

namespace EIU\LLIntegration;

use EIU\LLIntegration\Registration\RegistrationRequest;

class Account
{
    private RegistrationRequest $registrationRequest;

    public function __construct($registrationRequest)
    {
        $this->registrationRequest = $registrationRequest;
    }

    public function setAccountData(): string
    {
        $data = [
            "account_name" => $this->registrationRequest->getUserName(),
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
}