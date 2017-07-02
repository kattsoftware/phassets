<?php

namespace Phassets\Interfaces;

use Phassets\Asset;
use Phassets\Exceptions\PhassetsInternalException;

/**
 * Deployer interface for defining deployers components
 *
 * This content is released under the MIT License (MIT).
 * @see LICENSE file
 */
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
     * then update the Asset instance's outputUrl property, without performing
     * any further filters' actions.
     *
     * @param Asset $asset
     * @return bool Whether the Asset was previously deployed or not;
     *              If yes, then Asset's outputUrl property will be updated.
     */
    public function isPreviouslyDeployed(Asset $asset);

    /**
     * Given an Asset instance, try to deploy is using internal
     * rules of this deployer and update Asset's property outputUrl.
     *
     * @param Asset $asset Asset instance whose outputUrl property will be modified
     * @throws PhassetsInternalException If the deployment process fails
     */
    public function deploy(Asset $asset);

    /**
     * This must throw a PhassetsInternalException if the current configuration
     * doesn't allow this deployer to deploy processed assets.
     *
     * @throws PhassetsInternalException If at this time Phassets can't use this deployer to
     *                                   deploy and serve deployed assets
     */
    public function isSupported();
}