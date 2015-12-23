<?php

namespace Alius\Config\Exceptions;

class FileNotReadable extends ConfigException
{
    /**
     * File is not readable
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct('File is not readable: "' . $name . '"');
    }
}
