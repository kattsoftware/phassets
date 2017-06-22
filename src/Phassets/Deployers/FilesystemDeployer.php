<?php

namespace Phassets\Deployers;

use Phassets\Asset;
use Phassets\Interfaces\CacheAdapter;
use Phassets\Interfaces\Configurator;
use Phassets\Interfaces\Deployer;

class FilesystemDeployer implements Deployer
{
    /**
     * @var Configurator Loaded configurator
     */
    private $configurator;

    /**
     * @var CacheAdapter Loaded cache adapter
     */
    private $cacheAdapter;

    /**
     * @var string Where should be the assets deployed
     */
    private $destinationPath;

    /**
     * @var string the URL prefix used for creating the full
     */
    private $baseUrl;

    /**
     * Deployer constructor.
     *
     * @param Configurator $configurator Chosen and loaded Phassets configurator.
     * @param CacheAdapter $cacheAdapter Chosen and loaded Phassets cache adapter (if any)
     */
    public function __construct(Configurator $configurator, CacheAdapter $cacheAdapter)
    {
        $this->configurator = $configurator;
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * Attempt to retrieve a previously deployed asset; if it does exist,
     * then return an absolute URL to its deployed version without performing
     * any further filters' actions.
     *
     * @param Asset $asset
     * @return string|bool An absolute URL to asset already-processed version or false
     *                     if the asset was never deployed using this class.
     */
    public function getDeployedFile(Asset $asset)
    {
        // Is there any previous deployed version?
        $outputBasename = $this->computeOutputBasename($asset);
        $file = $this->destinationPath . DIRECTORY_SEPARATOR . $outputBasename;

        if (is_file($file)) {
            return $this->baseUrl . '/' . $outputBasename;
        }

        return false;
    }

    /**
     * Given an Asset instance, try to deploy the file using internal
     * rules of this deployer. Returns false in case of failure.
     *
     * @param Asset $asset
     * @return string|bool An absolute URL to asset already-processed version or false
     *                     if the asset wasn't deployed.
     */
    public function deploy(Asset $asset)
    {
        $outputBasename = $this->computeOutputBasename($asset);
        $fullPath = $this->destinationPath . DIRECTORY_SEPARATOR . $outputBasename;

        $saving = file_put_contents($fullPath, $asset->getContents());

        if($saving === false) {
            return false;
        }

        return $this->baseUrl . '/' . $outputBasename;
    }

    /**
     * This must return true/false if the current configuration allows
     * this deployer to deploy processed assets AND it can return previously
     * deployed assets as well.
     *
     * @return bool True if at this time Phassets can use this deployer to
     *              deploy and serve deployed assets, false otherwise.
     */
    public function isSupported()
    {
        $this->destinationPath = $this->configurator->getConfig('filesystem_deployer', 'destination_path');
        $this->baseUrl = $this->configurator->getConfig('filesystem_deployer', 'base_url');

        if ($this->destinationPath === null) {
            return false;
        }

        if (!is_dir($this->destinationPath) || !is_writable($this->destinationPath)) {
            return false;
        }

        return true;
    }

    /**
     * Generates the output full file name of an Asset instance.
     * Pattern: <original_file_name>_<last_modified_timestamp>[.<extension>]
     *
     * @param Asset $asset
     * @return string Generated basename of asset
     */
    private function computeOutputBasename(Asset $asset)
    {
        $ext = $asset->getExtension() ? '.' . $asset->getExtension() : '';

        return $asset->getFilename() . '_' . $asset->getModifiedTimestamp() . $ext;
    }
}