<?php

declare(strict_types=1);

namespace EIU\LLIntegration\Resource;

use EIU\LLIntegration\RequestResource\IdentificationRequest;
use stdClass;

/**
 * Provides a simple wrapper around a LibLynx identification resource
 * @package EIU\LLIntegration
 *
 * @property stdClass $id
 * @property stdClass $ip
 * @property stdClass $url
 * @property stdClass $status
 */
class Identification extends AbstractApiResource
{
    /**
     * Get the status of the identification.
     *
     * @return stdClass Status of the identification.
     */
    public function getStatus(): stdClass
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function requiresWayf(): bool
    {
        return $this->status == 'wayf';
    }

    /**
     * @return string
     */
    public function getWayfUrl(): string
    {
        return $this->getLink('wayf');
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
     * @return stdClass|null
     */
    public function getId(): ?stdClass
    {
        return $this->id ?? null;
    }

    /**
     * @param $linkName
     * @return null
     */
    public function getLink($linkName): mixed
    {
        return $this->_links->$linkName->href ?? null;
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
