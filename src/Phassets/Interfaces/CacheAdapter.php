<?php

namespace Phassets\Interfaces;

interface CacheAdapter
{
    /**
     * CacheAdapter constructor.
     *
     * @param Configurator $configurator Chosen and loaded Phassets configurator.
     */
    public function __construct(Configurator $configurator);

    /**
     * Save a value in cache for a giving key and return that value,
     * or false if the saving fails.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed|bool The saved value or false if saving cannot be performed
     */
    public function save($key, $value);

    /**
     * Return the cached value for a given key or false on failure.
     *
     * @param string $key
     * @return mixed The cache value or false on failure
     */
    public function get($key);
}