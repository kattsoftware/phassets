<?php

namespace Phassets\CacheAdapters;

use Phassets\Interfaces\CacheAdapter;
use Phassets\Interfaces\Configurator;

class CodeIgniterCacheAdapter implements CacheAdapter
{
    /**
     * @var int Number of seconds until a cache entry expires
     */
    private $ttl;

    /**
     * @var CI_Controller CodeIgniter instance
     */
    private $ci;

    /**
     * CacheAdapter constructor.
     *
     * @param Configurator $configurator Chosen and loaded Phassets configurator.
     */
    public function __construct(Configurator $configurator)
    {
        $this->ttl = $configurator->getConfig('cache', 'ttl');

        $this->ci = &get_instance();
    }

    /**
     * Save a value in cache for a giving key and return that value,
     * or false if the saving fails.
     *
     * @param string $key
     * @param mixed  $value
     * @return mixed|bool The saved value or false if saving cannot be performed
     */
    public function save($key, $value)
    {
        $success = $this->ci->cache->save($key, $value, $this->ttl);

        return $success ? $value : false;
    }

    /**
     * Return the cached value for a given key or false on failure.
     *
     * @param string $key
     * @return mixed The cache value or false on failure
     */
    public function get($key)
    {
        return $this->ci->cache->get($key);
    }
}