<?php

namespace Phassets;

use Phassets\CacheAdapters\DummyCacheAdapter;
use Phassets\Exceptions\PhassetsInternalException;
use Phassets\Interfaces\CacheAdapter;
use Phassets\Interfaces\Configurator;
use Phassets\Interfaces\Deployer;
use Phassets\Interfaces\Filter;
use Phassets\Interfaces\Logger;
use Phassets\Loggers\DummyLogger;

/**
 * Phassets library
 *
 * This content is released under the MIT License (MIT).
 * @see LICENSE file
 */
class Phassets
{
    /**
     * @var Factory
     */
    private $objectsFactory;

    /**
     * @var Configurator
     */
    private $loadedConfigurator;

    /**
     * @var CacheAdapter
     */
    private $loadedCacheAdapter;

    /**
     * @var Logger
     */
    private $loadedLogger;

    /**
     * @var array The assets_source config value (array of paths)
     */
    private $assetsSource;

    /**
     * @var string Loaded deployer for global usage
     */
    private $loadedDeployer;

    /**
     * @var array "filters" setting from Configurator
     */
    private $filters;

    /**
     * @var Deployer[] Associative array of deployers class names and their instances.
     */
    private $deployersInstances = [];

    /**
     * @var Filter[] Associative array of class names and their instances.
     */
    private $filtersInstances = [];

    /**
     * AssetsManager constructor.
     *
     * @param Configurator|string $configurator
     * @param Logger|string $logger
     * @param CacheAdapter|string $cacheAdapter
     */
    public function __construct($configurator, $logger = null, $cacheAdapter = null)
    {
        $this->objectsFactory = new Factory();

        $this->setConfigurator($configurator);

        $this->setLogger($logger);
        $this->setCacheAdapter($cacheAdapter);

        $this->readConfig();
    }

    /**
     * Given a full-path of a file/arbitrary string, creates an instance
     * of Asset.
     *
     * @param string $file
     * @return Asset Generated instance for $file
     */
    public function createAsset($file)
    {
        foreach ($this->assetsSource as $source) {
            if (is_file($source . DIRECTORY_SEPARATOR . $file)) {
                return $this->objectsFactory->buildAsset($source . DIRECTORY_SEPARATOR . $file);
            }
        }

        return $this->objectsFactory->buildAsset($file);
    }

    /**
     * Processes and deploys an Asset instance and returns the same instance
     * with modified properties, according to used filters and deployers.
     *
     * @param Asset $asset Asset to be processed & deployed
     * @param null|array $customFilters Overrides "filter" setting
     * @param null|string $customDeployer Overrides the loaded deployer
     *
     * @return Asset Modified instance of the received Asset
     */
    public function work(Asset $asset, $customFilters = null, $customDeployer = null)
    {
        // See if file is already deployed.
        if ($customDeployer !== null) {
            if ($this->loadDeployer($customDeployer)) {
                if ($this->deployersInstances[$customDeployer]->isPreviouslyDeployed($asset)) {
                    return $asset;
                }
            }
        } elseif ($this->loadDeployer($this->loadedDeployer)) {
            if ($this->deployersInstances[$this->loadedDeployer]->isPreviouslyDeployed($asset)) {
                return $asset;
            }
        }

        // No previous deployed version found, let's create it now!
        // First step: pass the asset through all filters.
        if (is_array($customFilters)) {
            $filters = $customFilters;
        } else {
            $ext = $asset->getExtension();
            $filters = isset($this->filters[$ext]) ? $this->filters[$ext] : null;
        }

        if (is_array($filters)) {
            foreach ($filters as $filter) {
                if ($this->loadFilter($filter)) {
                    try {
                        $this->filtersInstances[$filter]->filter($asset);
                    } catch (PhassetsInternalException $e) {
                        $this->loadedLogger->error('An error occurred while filtering the asset: ' . $e);
                    }
                }
            }
        }

        // All set! Let's deploy now.
        try {
            if ($customDeployer !== null && $this->loadDeployer($customDeployer)) {
                $this->deployersInstances[$customDeployer]->deploy($asset);
            } elseif ($this->loadDeployer($this->loadedDeployer)) {
                $this->deployersInstances[$this->loadedDeployer]->deploy($asset);
            }
        } catch (PhassetsInternalException $e) {
            $this->loadedLogger->error('An error occurred while deploying the asset: ' . $e);
        }

        return $asset;
    }

    /**
     * @param Configurator|string $configurator
     */
    public function setConfigurator($configurator)
    {
        if ($configurator instanceof Configurator) {
            $this->loadedConfigurator = $configurator;
        } elseif (is_string($configurator)) {
            $this->loadedConfigurator = $this->objectsFactory->buildConfigurator($configurator);
        }
    }

    /**
     * @param Logger|string $logger
     */
    public function setLogger($logger)
    {
        if ($logger instanceof Logger) {
            $this->loadedLogger = $logger;
        } elseif (is_string($logger)) {
            $this->loadedLogger = $this->objectsFactory->buildLogger($logger, $this->loadedConfigurator);
        }
    }

    /**
     * @param CacheAdapter|string $cacheAdapter
     */
    public function setCacheAdapter($cacheAdapter)
    {
        if ($cacheAdapter instanceof CacheAdapter) {
            $this->loadedCacheAdapter = $cacheAdapter;
        } elseif (is_string($cacheAdapter)) {
            $this->loadedCacheAdapter = $this->objectsFactory->buildCacheAdapter(
                $cacheAdapter,
                $this->loadedConfigurator
            );
        }
    }

    /**
     * Try to create & load an instance of a given Filter class name.
     *
     * @param string $class
     * @return bool Whether the loading succeeded or not
     */
    private function loadFilter($class)
    {
        if (isset($this->filtersInstances[$class])) {
            return true;
        }

        $filter = $this->objectsFactory->buildFilter($class, $this->loadedConfigurator);

        if ($filter === false) {
            $this->loadedLogger->warning('Could not load ' . $class . ' filter.');

            return false;
        }

        $this->filtersInstances[$class] = $filter;
        $this->loadedLogger->debug('Filter ' . $class . ' found & loaded.');

        return true;
    }

    /**
     * Try to create & load an instance of a given Deployer class name.
     *
     * @param string $class
     * @return bool Whether the loading succeeded or not
     */
    private function loadDeployer($class)
    {
        if (isset($this->deployersInstances[$class])) {
            return true;
        }

        $deployer = $this->objectsFactory->buildDeployer($class, $this->loadedConfigurator, $this->loadedCacheAdapter);

        if ($deployer === false) {
            $this->loadedLogger->warning('Could not load ' . $class . ' deployer.');

            return false;
        }

        try {
            $deployer->isSupported();
        } catch (PhassetsInternalException $e) {
            $this->loadedLogger->warning($class . ' deployer is not supported: ' . $e);
        }

        $this->deployersInstances[$class] = $deployer;
        $this->loadedLogger->debug('Deployer ' . $class . ' found & loaded.');

        return true;
    }

    /**
     * After the Configurator was loaded, try to complete the load of other settings.
     */
    private function readConfig()
    {
        if ($this->loadedLogger === null) {
            $logger = $this->loadedConfigurator->getConfig('logger', 'adapter');

            $this->setLogger($logger);

            if ($this->loadedLogger === null) {
                $this->setLogger(DummyLogger::class);
            }
        }

        if ($this->loadedCacheAdapter === null) {
            $cacheAdapter = $this->loadedConfigurator->getConfig('cache', 'adapter');

            $this->setCacheAdapter($cacheAdapter);

            if ($this->loadedCacheAdapter === null) {
                $this->setCacheAdapter(DummyCacheAdapter::class);
            }
        }

        // Configuration loading
        // Look-up for assets_source
        $this->assetsSource = $this->loadedConfigurator->getConfig('assets_source');

        if (empty($this->assetsSource)) {
            $this->loadedLogger->warning('Could not load the "assets_source" setting.');
        }

        // "assets_source" can be an array of paths.
        $this->assetsSource = (array)$this->assetsSource;

        // Loading the filters
        $filters = $this->loadedConfigurator->getConfig('filters');

        if (is_array($filters)) {
            $this->filters = $filters;

            foreach ($filters as $extensionFilters) {
                foreach ($extensionFilters as $filter) {
                    $this->loadFilter($filter);
                }
            }
        } else {
            $this->loadedLogger->warning('"filters" setting is not an array; no filters loaded...');
        }

        // Loading the main or backup deployer
        $deployers = $this->loadedConfigurator->getConfig('deployers');

        if (is_array($deployers)) {
            if (isset($deployers['main'])) {
                $backupLoaded = $this->loadDeployer($deployers['main']);

                if (!$backupLoaded) {
                    $this->loadedLogger->warning('Could not load the main deployer ' . $deployers['main']);
                } else {
                    $this->loadedDeployer = $deployers['main'];

                    $this->loadedLogger->debug('Main deployer loaded: ' . $deployers['main']);
                }
            } else {
                $this->loadedLogger->warning('There is no main deployer set in configuration.');
            }

            if ($this->loadedDeployer === null) {
                if (isset($deployers['backup'])) {
                    $backupLoaded = $this->loadDeployer($deployers['backup']);

                    if (!$backupLoaded) {
                        $this->loadedLogger->warning('Could not load the backup deployer ' . $deployers['backup']);
                    } else {
                        $this->loadedDeployer = $deployers['backup'];

                        $this->loadedLogger->debug('Backup deployer loaded: ' . $deployers['backup']);
                    }
                } else {
                    $this->loadedLogger->warning('There is no backup deployer set in configuration.');
                }
            }
        } else {
            $this->loadedLogger->warning('"deployers" setting is not an array; no deployers loaded...');
        }
    }
}