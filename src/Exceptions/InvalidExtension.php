<?php

namespace Alius\Config\Exceptions;

class InvalidExtension extends ConfigException
{
    /**
     * Extension is not php
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct('Extension is not php: "' . $name . '"');
    }
}
