<?php

namespace Phassets;

use Phassets\Interfaces\CacheAdapter;
use Phassets\Interfaces\Configurator;
use Phassets\Interfaces\Deployer;
use Phassets\Interfaces\Filter;
use Phassets\Interfaces\Logger;

class Factory
{
    /**
     * Creates a Configurator instance.
     *
     * @param string $class Fully qualified class name of a Configurator class
     *
     * @return Configurator|bool Created instance of the provided Configurator;
     *                           false on failure
     */
    public function buildConfigurator($class)
    {
        if (!class_exists($class) || !is_subclass_of($class, Configurator::class)) {
            $class = "\\Phassets\\Config\\$class";
        }

        if (class_exists($class) && is_subclass_of($class, Configurator::class)) {
            return new $class;
        }

        return false;
    }

    /**
     * Creates a Logger instance.
     *
     * @param string $class Fully qualified class name of a Logger class
     * @param Configurator $configurator Currently used Configurator of Phassets
     *
     * @return bool|Logger Created instance of the provided Logger; false on failure
     */
    public function buildLogger($class, Configurator $configurator)
    {
        if (!class_exists($class) || !is_subclass_of($class, Logger::class)) {
            $class = "\\Phassets\\Loggers\\$class";
        }

        if (class_exists($class) && is_subclass_of($class, Logger::class)) {
            return new $class($configurator);
        }

        return false;
    }

    /**
     * Creates a CacheAdapter instance.
     *
     * @param string $class Fully qualified class name of a CacheAdapter class
     * @param Configurator $configurator Currently used Configurator of Phassets
     *
     * @return bool|CacheAdapter Created instance of the provided CacheAdapter; false on failure
     */
    public function buildCacheAdapter($class, Configurator $configurator)
    {
        if (!class_exists($class) || !is_subclass_of($class, CacheAdapter::class)) {
            $class = "\\Phassets\\CacheAdapters\\$class";
        }

        if (class_exists($class) && is_subclass_of($class, CacheAdapter::class)) {
            return new $class($configurator);
        }

        return false;
    }

    /**
     * Creates a new Asset instance, by using an absolute file path.
     *
     * @param string $file
     * @return Asset
     */
    public function buildAsset($file)
    {
        return new Asset($file);
    }

    /**
     * Creates a Deployer instance.
     *
     * @param string $class Fully qualified class name of a Deployer class
     * @param Configurator $configurator Currently used Configurator of Phassets
     * @param CacheAdapter $cacheAdapter Currently used CacheAdapter of Phassets
     *
     * @return Deployer|bool Created instance of the provided Deployer; false on failure
     */
    public function buildDeployer($class, Configurator $configurator, CacheAdapter $cacheAdapter)
    {
        if (!class_exists($class) || !is_subclass_of($class, Deployer::class)) {
            $class = "\\Phassets\\Deployers\\$class";
        }

        if (class_exists($class) && is_subclass_of($class, Deployer::class)) {
            return new $class($configurator, $cacheAdapter);
        }

        return false;
    }

    /**
     * Creates a Filter instance.
     *
     * @param string $class Fully qualified class name of a Filter class
     * @param Configurator $configurator Currently used Configurator of Phassets
     *
     * @return Filter|bool Created instance of the provided Filter; false on failure
     */
    public function buildFilter($class, Configurator $configurator)
    {
        if (!class_exists($class) || !is_subclass_of($class, Filter::class)) {
            $class = "\\Phassets\\Filters\\$class";
        }

        if (class_exists($class) && is_subclass_of($class, Filter::class)) {
            return new $class($configurator);
        }

        return false;
    }
}
