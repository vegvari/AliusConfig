<?php

namespace Alius\Config;

use SplFileInfo;
use FilesystemIterator;
use RecursiveDirectoryIterator;

class Config
{
    /**
     * List of required config variables
     * @var array
     */
    protected $required = [];

    /**
     * List of config variables
     * @var array
     */
    protected $config = [];

    /**
     * List of files parsed (using real path as key)
     * @var array
     */
    protected $files = [];

    /**
     * @param string|array $paths
     */
    public function __construct($paths, array $required = [])
    {
        $paths = is_array($paths) ? $paths : [$paths];

        foreach ($paths as $entity) {
            $this->read($entity);
        }

        if (! empty($required)) {
            $this->required($required);
        }
    }

    /**
     * Return a config value
     *
     * @param  string $name
     * @return mixed
     */
    public function get($name)
    {
        if (! isset($this->config[$name])) {
            throw ConfigException::missingVariable($name);
        }

        return $this->config[$name]['value'];
    }

    /**
     * Return the filename of the variable
     *
     * @param  string $name
     * @return string
     */
    public function getOrigin($name)
    {
        if (! isset($this->config[$name])) {
            throw ConfigException::missingVariable($name);
        }

        return $this->config[$name]['origin'];
    }

    /**
     * Add required
     *
     * @param string|array $required
     */
    public function required($required)
    {
        $required = is_array($required) ? $required : [$required];

        foreach ($required as $key => $value) {
            $this->required[] = $value;
            if (! isset($this->config[$value])) {
                throw ConfigException::missingRequired($value);
            }
        }
    }

    /**
     * Is this variable required?
     *
     * @param  string $name
     * @return bool
     */
    public function isRequired($name)
    {
        return array_search($name, $this->required, true) !== false;
    }

    /**
     * Return the list of included files
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Read a file or directory
     *
     * @param string $entity
     */
    protected function read($entity)
    {
        if (! file_exists($entity)) {
            throw ConfigException::notFound($entity);
        }

        if (! is_readable($entity)) {
            throw ConfigException::notReadable($entity);
        }

        if (is_dir($entity)) {
            $this->readDir(new RecursiveDirectoryIterator($entity, FilesystemIterator::FOLLOW_SYMLINKS));
        } elseif (is_file($entity)) {
            $this->readFile(new SplFileInfo($entity));
        }
    }

    /**
     * Process a file
     *
     * @param SplFileInfo $file
     */
    protected function readFile(SplFileInfo $file)
    {
        if ($file->getExtension() !== 'php') {
            throw ConfigException::wrongExtension($file->getRealPath());
        }

        $content = include $file->getRealPath();

        if (! is_array($content)) {
            throw ConfigException::contentIsNotArray($file->getRealPath());
        }

        $this->files[$file->getRealPath()] = $content;

        foreach ($content as $key => $value) {
            $this->config[$key]['value'] = $value;
            $this->config[$key]['origin'] = $file->getRealPath();
        }
    }

    /**
     * Process a directory
     *
     * @param string $dir
     */
    protected function readDir(RecursiveDirectoryIterator $iterator)
    {
        foreach ($iterator as $entry) {
            if ($entry->getFilename() !== '.' && $entry->getFilename() !== '..') {
                $this->read($entry);
            }
        }
    }

    /**
     * Get the variables in one array
     *
     * @return array
     */
    public function compile()
    {
        $result = [];

        foreach ($this->config as $key => $value) {
            $result[$key] = $value['value'];
        }

        return $result;
    }
}
