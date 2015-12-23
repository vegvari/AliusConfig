<?php

namespace Alius\Config\Exceptions;

class UndefinedVariable extends ConfigException
{
    /**
     * Variable is not defined
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct('Config variable is not defined: "' . $name . '"');
    }
}
