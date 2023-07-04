<?php

declare(strict_types=1);

namespace EIU\LLIntegration;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Identification

{
    public string $status;
    public array $unit_requests;
    private Client $client;
    private LoggerInterface $log;
    private Request $request;

    public function __construct($request)
    {
        $this->client = new Client();
        $this->log = new NullLogger();
        $this->request = $request;
    }


    /**
     * Authorize given authentication object
     * @return Identification|null
     */
    public function authenticate(): Identification|null
    {
        $payload = $this->request->getRequestData();
        $response = $this->client->apiPOST('@new_identification', $payload);
        if (!isset($response->id)) {
            //failed
            $this->log->critical('Identification request failed {payload}', ['payload' => $payload]);
            return null;
        }

        $identification = new Identification($response);
        $this->log->info(
            'Identification request for ip {ip} on URL {url} succeeded status={status} id={id}',
            [
                'status' => $identification->status,
                'id' => $identification->id,
                'ip' => $identification->ip,
                'url' => $identification->url,
            ]
        );

        return $identification;
    }

    public static function fromJSON($response)
    {
        $id = new Identification();
        $id->updateFromJSON($response);
        return $id;
    }

    public function addAuthorizationRequest($authorizationUnit, $right='view')
    {
        $this->unit_requests[]= array(
            'unit_code' => $authorizationUnit,
            'operations' => array(
                array('type' => $right)
            )
        );
    }

    public function updateFromJSON($response)
    {
        $vars = get_object_vars($response);
        foreach ($vars as $name => $value) {
            $this->$name = $value;
        }
        return $this;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function isIdentified()
    {
        return $this->status == 'identified';
    }

    public function mustAgreeTerms()
    {
        return isset($this->terms) && ($this->terms==='not-agreed');
    }

    public function setTermsAgreed()
    {
        $this->terms='agreed';
    }

    public function getTermsUrl()
    {
        return (($this->mustAgreeTerms()) && isset($this->_links->terms->href)) ? $this->_links->terms->href : null;
    }

    public function requiresWayf()
    {
        return $this->status == 'wayf';
    }

    public function getAccountName()
    {
        if ($this->status == 'identified') {
            return $this->account->account_name;
        } else {
            return null;
        }
    }

    /**
     * Get the publisher's identifier for an account
     * @return string
     */
    public function getAccountIdentifier()
    {
        if ($this->status == 'identified') {
            return isset($this->account->publisher_reference) ? $this->account->publisher_reference : null;
        } else {
            return null;
        }
    }

    public function getId()
    {
        return isset($this->id) ? $this->id : null;
    }

    public function getLink($linkName)
    {
        return isset($this->_links->$linkName->href) ? $this->_links->$linkName->href : null;
    }

    public function getWayfUrl()
    {
        return (($this->status == 'wayf') && isset($this->_links->wayf->href)) ? $this->_links->wayf->href : null;
    }

    public function getUnauthorizedUrl($wantedUnit)
    {
        $url=null;
        if (isset($this->_links->unauthorized->href))
        {
            $url=$this->_links->unauthorized->href;
            $url.="?unit=".urlencode($wantedUnit);
        }
        return $url;
    }

    public function doWayfRedirect()
    {
        $url = $this->getWayfUrl();
        if (!is_null($url)) {
            header("Location: $url");
            exit;
        }
    }

    public function getAuthorization($unitContent, $type = 'view')
    {
        if (isset($this->authorizations->$unitContent->$type)) {
            return $this->authorizations->$unitContent->$type;
        } else {
            return null;
        }
    }

    public function getAuthorizedUnits($type = 'view')
    {
        $units=array();
        if (isset($this->authorizations)){

            foreach($this->authorizations as $unit=>$auth) {

                if ($auth->$type=='authorized') {
                    $units[]=$unit;
                }
            }
        }

        return $units;
    }
}