<?php

declare(strict_types=1);

namespace EIU\LLIntegration;
class Main
{
    private string $name;
    public function __construct($name)
    {
        $this->name = $name;
        echo 'hello, ' . $this->name . '!!';
    }
}