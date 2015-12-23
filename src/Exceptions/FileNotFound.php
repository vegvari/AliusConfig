<?php

namespace Alius\Config\Exceptions;

class FileNotFound extends ConfigException
{
    /**
     * File or directory not found
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct('File or directory not found: "' . $name . '"');
    }
}
