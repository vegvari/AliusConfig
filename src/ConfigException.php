<?php

namespace Alius\Config;

use Exception;

class ConfigException extends Exception
{
    /**
     * file or directory not found
     *
     * @param  string $name
     * @return this
     */
    public static function notFound($name)
    {
        return new static('File or directory not found: "' . $name . '"');
    }

    /**
     * File is not readable
     *
     * @param  string $name
     * @return this
     */
    public static function notReadable($name)
    {
        return new static('File is not readable: "' . $name . '"');
    }

    /**
     * Extension is not php
     *
     * @param  string $name
     * @return this
     */
    public static function wrongExtension($name)
    {
        return new static('Extension is not php: "' . $name . '"');
    }

    /**
     * File is not returning an array
     *
     * @param  string $name
     * @return this
     */
    public static function contentIsNotArray($name)
    {
        return new static('Config files must return an array: "' . $name . '"');
    }

    /**
     * Required config variable is not defined
     *
     * @param  string $name
     * @return this
     */
    public static function missingRequired($name)
    {
        return new static('Required config variable is not defined: "' . $name . '"');
    }

    /**
     * Variable is not defined
     *
     * @param  string $name
     * @return this
     */
    public static function missingVariable($name)
    {
        return new static('Config variable is not defined: "' . $name . '"');
    }
}
