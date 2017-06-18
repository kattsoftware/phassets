<?php

namespace Phassets\Interfaces;

use Phassets\Asset;

interface Deployer
{
    /**
     * Deployer constructor.
     *
     * @param Configurator $configurator Chosen and loaded Phassets configurator.
     * @param CacheAdapter $cacheAdapter Chosen and loaded Phassets cache adapter (if any)
     */
    public function __construct(Configurator $configurator, CacheAdapter $cacheAdapter);

    /**
     * Attempt to retrieve a previously deployed asset; if it does exist,
     * then return an absolute URL to its deployed version without performing
     * any further filters' actions.
     *
     * @param Asset $asset
     * @return string|bool An absolute URL to asset already-processed version or false
     *                     if the asset was never deployed using this class.
     */
    public function getDeployedFile(Asset $asset);

    /**
     *
     *
     * @param Asset $asset
     * @return string|bool An absolute URL to asset already-processed version or false
     *                     if the asset wasn't deployed.
     */
    public function deploy(Asset $asset);

    /**
     * This must return true/false if the current configuration allows
     * this deployer to deploy processed assets AND it can return previously
     * deployed assets as well.
     *
     * @return bool True if at this time Phassets can use this deployer to
     *              deploy and serve deployed assets, false otherwise.
     */
    public function isSupported();
}