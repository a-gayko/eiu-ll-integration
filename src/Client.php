<?php

declare(strict_types=1);

namespace EIU\LLIntegration;

class Client
{
    const TRANSIENT_TOKEN = 'liblynx_token';
    const TRANSIENT_ENTRYPOINT = 'liblynx_entrypoint';
    protected string $apiroot;

    public function __construct()
    {
        $this->apiroot = 'http://connect.liblynx.com';
    }

    /**
     * request new token
     */
    protected function newToken()
    {
        $url = $this->apiroot.'/oauth/v2/token';
        $authHdr = "Basic ".base64_encode(LIBLYNX_CLIENT_KEY . ':' . LIBLYNX_CLIENT_SECRET);

        $response = wp_remote_post(
            $url,
            array(
                'method'   => 'POST',
                'timeout'  => 15,
                'blocking' => true,
                'headers'  => array('Authorization' => $authHdr),
                'body'     => array('grant_type' => 'client_credentials'),
            )
        );
        if (isset($response['response']['code']) && ($response['response']['code'] == 200)) {
            $token = json_decode($response['body']);

            return $token;
        }

        //failed
        return null;
    }

    /**
     * Obtain OAuth token from Wordpress transient, fetching
     * a new one if its expired
     */
    public function getToken()
    {
        $token = get_transient(self::TRANSIENT_TOKEN, null);
        if (!empty($token)) {
            return $token;
        }

        $oauth = $this->newToken();
        if ($oauth) {
            set_transient(self::TRANSIENT_TOKEN, $oauth->access_token, $oauth->expires_in - 60);
            $token = $oauth->access_token;
        }

        return $token;
    }

    /**
     * URLs are discovered through the entrypoint resource, so code can
     * make calls to @new_identification and we'll figure out what URL to
     * to use. If the url doesn't start with @, then its returned unchanged
     */
    protected function transformUrl($url)
    {
        if ($url[0] == '@') {
            $entrypoint = $this->getEntrypoint();
            if (isset($entrypoint->_links->$url)) {
                $url = $entrypoint->_links->$url->href;
            }
        }

        return $url;
    }

    /**
     * Make OAuth secured API call
     */
    protected function callAPI($url, $method = 'GET', $jsonBody = null)
    {
        $token = $this->getToken();
        $authHdr = "Bearer ".$token;

        //tranform the $url if shorthand
        $url = $this->transformUrl($url);

        $params = array(
            'method'   => $method,
            'timeout'  => 15,
            'blocking' => true,
            'headers'  => array(
                'Authorization' => $authHdr,
                'Accept'        => 'application/json',
            ),
        );
        if (!is_null($jsonBody)) {
            $params['body'] = $jsonBody;
            $params['headers']['Content-Type'] = 'application/json';
        }

        $response = wp_remote_post($url, $params);
        if (is_wp_error($response)) {
            $msg = sprintf(
                'request for %s with args %s failed: errors=%s',
                $url,
                print_r($params, true),
                print_r($response->get_error_messages(), true)
            );
            error_log($msg);

            return null;
        }

        if (isset($response['response']['code']) &&
            ($response['response']['code'] >= 200) &&
            ($response['response']['code'] < 300)) {
            $data = json_decode($response['body']);

            return $data;
        }

        return null;
    }

    /**
     * Shorthand method for API GET
     */
    protected function apiGET($url)
    {
        return $this->callAPI($url, 'GET');
    }

    /**
     * Shorthand method for API POST
     */
    public function apiPOST($url, $jsonBody)
    {
        return $this->callAPI($url, 'POST', $jsonBody);
    }

    public function getEntryPoint()
    {
        $json = get_transient(self::TRANSIENT_ENTRYPOINT);
        if (!empty($json)) {
            return json_decode($json);
        }

        $url = $this->apiroot.'/api';
        $entrypoint = $this->apiGET($url);
        if ($entrypoint) {
            set_transient(self::TRANSIENT_ENTRYPOINT, json_encode($entrypoint), 86400);
        }

        return $entrypoint;
    }
}