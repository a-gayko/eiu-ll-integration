<?php

declare(strict_types=1);

namespace EIU\LLIntegration\Resource;

use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;
use stdClass;

/**
 * @package EIU\LLIntegration
 *
 */
abstract class AbstractApiResource implements ApiResourceInterface
{
    /**
     * @param mixed $objectResponse
     */
    public function __construct(public mixed $objectResponse)
    {
        $this->objectResponse = (object)$objectResponse;
        $this->getFieldFromResponse();
    }

    /**
     * @return void
     */
    private function getFieldFromResponse(): void
    {
        $vars = get_object_vars($this->objectResponse);
        foreach ($vars as $name => $value) {
            $this->$name = $value;
        }
    }
}
