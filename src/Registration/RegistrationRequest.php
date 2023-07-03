<?php

declare(strict_types=1);

namespace EIU\LLIntegration\Registration;

class RegistrationRequest
{
    public function __construct()
    {
    }

    public function getRequestData()
    {
        $data = get_object_vars($this);
        foreach ($data as $key => $val) {
            if (is_null($val)) {
                unset($data[$key]);
            }
        }

        return json_encode($data);
    }

    public function getUserName(): string
    {
        $data = get_object_vars($this);
        $userName = $data['individual' ['display_name']];

        return json_encode($userName);
    }
}