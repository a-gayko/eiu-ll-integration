<?php

declare(strict_types=1);

namespace EIU\LLIntegration\Resource;

use EIU\LLIntegration\Resource\Interface\ApiResourceInterface;
use LogicException;
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

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        throw new LogicException("No value called $name");
    }

    /**
     * @param $name
     * @return mixed
     * @throws LogicException if link with name isn't present
     */
    public function getLink($name): mixed
    {
        if (isset($this->_links->$name)) {
            return $this->_links->$name->href;
        }
        throw new LogicException("resource did not contain a $name link");
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasLink($name): bool
    {
        return isset($this->_links->$name);
    }
}
