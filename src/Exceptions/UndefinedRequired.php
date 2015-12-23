<?php

namespace Alius\Config\Exceptions;

class UndefinedRequired extends ConfigException
{
    /**
     * Required config variable is not defined
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct('Required config variable is not defined: "' . $name . '"');
    }
}
