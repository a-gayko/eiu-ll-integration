<?php

declare(strict_types=1);

namespace EIU\LLIntegration\Identification;

class IdentificationRequest
{
    public string $url;
    public string $referrer;
    public string $user_agent;
    public string $ip;
    private array $unit_requests;

    public static function fromRequest()
    {
        $id = new IdentificationRequest();
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $id->ip = $_SERVER['REMOTE_ADDR'];
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            $id->referrer = $_SERVER['HTTP_REFERER'];
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $id->user_agent = $_SERVER['HTTP_USER_AGENT'];
        }
        if (isset($_SERVER['REQUEST_URI']) && isset($_SERVER['HTTP_HOST'])) {
            $id->url = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $id->url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        $id->unit_requests=array();

        return $id;
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
}