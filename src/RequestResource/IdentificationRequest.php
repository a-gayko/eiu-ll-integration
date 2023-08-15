<?php

declare(strict_types=1);

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Resource\Identification;
use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;

class IdentificationRequest extends AbstractApiRequest
{
    /**
     * @return string
     */
    public function getRequestDataJSON(): string
    {
        $data = [
            'ip'            => $_SERVER['REMOTE_ADDR'] ?? null,
            'referrer'      => $_SERVER['HTTP_REFERER'] ?? null,
            'user_agent'    => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'url'           => $_SERVER['HTTPS'] . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?? null,
            'unit_requests' => [],
        ];

        return json_encode($data);
    }

    /**
     * @return string
     */
    protected function getApiEndpoint(): string
    {
        return '@new_identification';
    }

    /**
     * @return string
     */
    protected function getLogMessage(): string
    {
        return 'Identification request failed {payload}';
    }

    /**
     * @param $response
     * @return ApiResourceInterface
     */
    protected function createResource($response): ApiResourceInterface
    {
        return new Identification($response);
    }

    /**
     * @return string
     */
    protected function getSuccessLogMessage(): string
    {
        return 'Identification request for ip {ip} on URL {url} succeeded status={status} id={id}';
    }

    /**
     * @param ApiResourceInterface $resource
     * @return array
     */
    protected function getSuccessLogContext(ApiResourceInterface $resource): array
    {
        return [
            'status' => $resource->status,
            'id'     => $resource->id,
            'ip'     => $resource->ip,
            'url'    => $resource->url,
        ];
    }

    /**
     * @param $response
     * @return IdentificationRequest
     */
    public static function fromJSON($response): IdentificationRequest
    {
        $id = new IdentificationRequest();
        $id->updateFromJSON($response);

        return $id;
    }

    /**
     * @param $authorizationUnit
     * @param $right
     * @return void
     */
    public function addAuthorizationRequest($authorizationUnit, $right = 'view'): void
    {
        $this->unit_requests[] = array(
            'unit_code'  => $authorizationUnit,
            'operations' => array(
                array('type' => $right),
            ),
        );
    }

    /**
     * @param $response
     * @return $this
     */
    public function updateFromJSON($response): self
    {
        $vars = get_object_vars($response);
        foreach ($vars as $name => $value) {
            $this->$name = $value;
        }

        return $this;
    }

    /**
     * @param $url
     * @return void
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return bool
     */
    public function isIdentified(): bool
    {
        return $this->status == 'identified';
    }

    /**
     * @return bool
     */
    public function mustAgreeTerms(): bool
    {
        return isset($this->terms) && ($this->terms === 'not-agreed');
    }

    /**
     * @return void
     */
    public function setTermsAgreed(): void
    {
        $this->terms = 'agreed';
    }

    /**
     * @return null
     */
    public function getTermsUrl()
    {
        return (($this->mustAgreeTerms()) && isset($this->_links->terms->href)) ? $this->_links->terms->href : null;
    }

    /**
     * @return bool
     */
    public function requiresWayf(): bool
    {
        return $this->status == 'wayf';
    }

    /**
     * @return string|null
     */
    public function getAccountName(): ?string
    {
        if ($this->status == 'identified') {
            return $this->account->account_name;
        } else {
            return null;
        }
    }

    /**
     * Get the publisher's identifier for an account
     * @return string|null
     */
    public function getAccountIdentifier(): ?string
    {
        if ($this->status == 'identified') {
            return $this->account->publisher_reference ?? null;
        } else {
            return null;
        }
    }

    /**
     * @return int | null
     */
    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    /**
     * @param $linkName
     * @return null
     */
    public function getLink($linkName)
    {
        return $this->_links->$linkName->href ?? null;
    }

    /**
     * @return null
     */
    public function getWayfUrl()
    {
        return (($this->status == 'wayf') && isset($this->_links->wayf->href)) ? $this->_links->wayf->href : null;
    }

    /**
     * @param $wantedUnit
     * @return string|null
     */
    public function getUnauthorizedUrl($wantedUnit): ?string
    {
        $url = null;
        if (isset($this->_links->unauthorized->href)) {
            $url = $this->_links->unauthorized->href;
            $url .= "?unit=" . urlencode($wantedUnit);
        }

        return $url;
    }

    /**
     * @return void
     */
    public function doWayfRedirect(): void
    {
        $url = $this->getWayfUrl();
        if (!is_null($url)) {
            header("Location: $url");
            exit;
        }
    }

    /**
     * @param $unitContent
     * @param string $type
     * @return null
     */
    public function getAuthorization($unitContent, string $type = 'view')
    {
        if (isset($this->authorizations->$unitContent->$type)) {
            return $this->authorizations->$unitContent->$type;
        } else {
            return null;
        }
    }

    /**
     * @param string $type
     * @return array
     */
    public function getAuthorizedUnits(string $type = 'view'): array
    {
        $units = array();
        if (isset($this->authorizations)) {
            foreach ($this->authorizations as $unit => $auth) {
                if ($auth->$type == 'authorized') {
                    $units[] = $unit;
                }
            }
        }

        return $units;
    }
}
