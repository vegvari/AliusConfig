<?php

namespace Alius\Config\Exceptions;

class InvalidContent extends ConfigException
{
    /**
     * File is not returning an array
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct('Config files must return an array: "' . $name . '"');
    }
}
