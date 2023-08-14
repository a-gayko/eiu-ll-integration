<?php

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Client\Client;
use EIU\LLIntegration\Interface\ApiRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 *
 */
abstract class AbstractApiRequest implements ApiRequestInterface
{
    protected Client $client;
    protected LoggerInterface $log;


    protected function __construct()
    {
        $this->client = new Client();
        $this->log = new NullLogger();
    }
}
