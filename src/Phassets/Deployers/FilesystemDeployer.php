<?php

namespace Phassets\Deployers;

use Phassets\Asset;
use Phassets\Exceptions\PhassetsInternalException;
use Phassets\Interfaces\CacheAdapter;
use Phassets\Interfaces\Configurator;
use Phassets\Interfaces\Deployer;

/**
 * Local filesystem deployer
 * @see Phassets GitHub wiki
 *
 * This content is released under the MIT License (MIT).
 * @see LICENSE file
 */
class FilesystemDeployer implements Deployer
{
    const CACHE_TTL = 3600;

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
     * @var string What should trigger re-deployment of an asset ('filemtime' (default), 'md5', 'sha1')
     */
    private $trigger;

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
     * then update the Asset instance's outputUrl property, without performing
     * any further filters' actions.
     * Note: only fullPath property of Asset instance is set, without any
     * pre-filtering.
     *
     * @param Asset $asset
     * @return bool Whether the Asset was previously deployed or not;
     *              If yes, then Asset's outputUrl property will be updated.
     */
    public function isPreviouslyDeployed(Asset $asset)
    {
        // Is there any previous deployed version?
        $cacheKey = $this->generateCacheKey($asset);

        $cachedUrl = $this->cacheAdapter->get($cacheKey);

        if ($cachedUrl !== false) {
            $asset->setOutputUrl($cachedUrl);

            return true;
        }

        $file = $this->destinationPath . DIRECTORY_SEPARATOR . $outputBasename;

        if (is_file($file)) {
            $objectUrl = $this->baseUrl . '/' . $outputBasename;
            $this->cacheAdapter->save($cacheKey, $objectUrl, self::CACHE_TTL);

            $asset->setOutputUrl($objectUrl);

            return true;
        }

        return false;
    }

    /**
     * Given an Asset instance, try to deploy is using internal
     * rules of this deployer and update Asset's property outputUrl.
     *
     * @param Asset $asset Asset instance whose outputUrl property will be modified
     * @throws PhassetsInternalException If the deployment process fails
     */
    public function deploy(Asset $asset)
    {
        $outputBasename = $this->computeOutputBasename($asset);
        $fullPath = $this->destinationPath . DIRECTORY_SEPARATOR . $outputBasename;

        $saving = file_put_contents($fullPath, $asset->getContents());

        if($saving === false) {
            throw new PhassetsInternalException(
                'file_put_contents() could not write to ' . $fullPath .
                '. It is writable?'
            );
        }

        $objectUrl = $this->baseUrl . '/' . $outputBasename;

        $asset->setOutputUrl($objectUrl);

        $this->cacheAdapter->save($this->generateCacheKey($outputBasename), $objectUrl, self::CACHE_TTL);
    }

    /**
     * This must throw a PhassetsInternalException if the current configuration
     * doesn't allow this deployer to deploy processed assets.
     *
     * @throws PhassetsInternalException If at this time Phassets can't use this deployer to
     *                                   deploy and serve deployed assets
     */
    public function isSupported()
    {
        $this->destinationPath = $this->configurator->getConfig('filesystem_deployer', 'destination_path');
        $this->baseUrl = $this->configurator->getConfig('filesystem_deployer', 'base_url');
        $this->trigger = $this->configurator->getConfig('filesystem_deployer', 'changes_trigger');

        if ($this->destinationPath === null) {
            throw new PhassetsInternalException('FilesystemDeployer: no "destination_path" setting found');
        }

        if (!is_dir($this->destinationPath) || !is_writable($this->destinationPath)) {
            throw new PhassetsInternalException("FilesystemDeployer: 'destination_path' ({$this->destinationPath}) is " .
            'either not a valid dir, nor writable.'
            );
        }
    }

    /**
     * Generates the output full file name of an Asset instance.
     * Pattern: <original_file_name>_<triggering_asset_value>[.<outputExtension>]
     *
     * @param Asset $asset
     * @return string Generated basename of asset
     */
    private function computeOutputBasename(Asset $asset)
    {
        $ext = $asset->getOutputExtension() ? '.' . $asset->getOutputExtension() : '';

        switch ($this->trigger) {
            case 'md5':
                $suffix = $asset->getMd5();
                break;
            case 'sha1':
                $suffix = $asset->getSha1();
                break;
            case 'filemtime':
            default:
                $suffix = $asset->getModifiedTimestamp();
        }

        return $asset->getFilename() . '_' . $suffix . $ext;
    }

    /**
     * CacheAdapter specific cache key.
     *
     * @param Asset $asset Instance for making the check.
     * @return string
     */
    private function generateCacheKey(Asset $asset)
    {
        return 'ph_fs_' . strtolower($asset->getFullPath());
    }
}