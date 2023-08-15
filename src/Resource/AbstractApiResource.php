<?php

declare(strict_types=1);

namespace EIU\LLIntegration\Resource;

use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;
use stdClass;

/**
 * @package EIU\LLIntegration
 *
 * @property stdClass $_links
 */
abstract class AbstractApiResource implements ApiResourceInterface
{
    /**
     * @param $obj
     */
    public function __construct($obj)
    {
        $vars = get_object_vars($obj);
        foreach ($vars as $name => $value) {
            $this->$name = $value;
        }
    }
}
