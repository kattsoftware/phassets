<?php

namespace Phassets;

use Phassets\Interfaces\CacheAdapter;
use Phassets\Interfaces\Configurator;
use Phassets\Interfaces\Deployer;
use Phassets\Interfaces\Filter;
use Phassets\Interfaces\Logger;

class Factory
{
    public function buildConfigurator($class)
    {
        if (!class_exists($class) || !is_subclass_of($class, Configurator::class)) {
            $class = "\\Phassets\\Config\\$class";
        }

        if(class_exists($class) && is_subclass_of($class, Configurator::class)) {
            return new $class;
        }

        return false;
    }

    public function buildLogger($class, Configurator $configurator)
    {
        if (!class_exists($class) || !is_subclass_of($class, Logger::class)) {
            $class = "\\Phassets\\Loggers\\$class";
        }

        if(class_exists($class) && is_subclass_of($class, Logger::class)) {
            return new $class($configurator);
        }

        return false;
    }

    public function buildCacheAdapter($class, Configurator $configurator)
    {
        if (!class_exists($class) || !is_subclass_of($class, CacheAdapter::class)) {
            $class = "\\Phassets\\CacheAdapters\\$class";
        }

        if(class_exists($class) && is_subclass_of($class, CacheAdapter::class)) {
            return new $class($configurator);
        }

        return false;
    }

    public function buildAsset($file)
    {
        return new Asset($file);
    }

    /**
     * @param       string $class
     * @param Configurator $configurator
     * @param CacheAdapter $cacheAdapter
     * @return Deployer|bool
     */
    public function buildDeployer($class, Configurator $configurator, CacheAdapter $cacheAdapter)
    {
        if (!class_exists($class) || !is_subclass_of($class, Deployer::class)) {
            $class = "\\Phassets\\Deployers\\$class";
        }

        if(class_exists($class) && is_subclass_of($class, Deployer::class)) {
            return new $class($configurator, $cacheAdapter);
        }

        return false;
    }

    public function buildFilter($class, Configurator $configurator)
    {
        if (!class_exists($class) || !is_subclass_of($class, Filter::class)) {
            $class = "\\Phassets\\Filters\\$class";
        }

        if(class_exists($class) && is_subclass_of($class, Filter::class)) {
            return new $class($configurator);
        }

        return false;
    }
}
