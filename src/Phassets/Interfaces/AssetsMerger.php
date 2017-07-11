<?php

namespace Phassets\Interfaces;

use Phassets\Asset;
use Phassets\Exceptions\PhassetsInternalException;

/**
 * Merger interface for defining merging multiple assets components
 *
 * This content is released under the MIT License (MIT).
 * @see LICENSE file
 */
interface AssetsMerger
{
    /**
     * AssetsMerger constructor.
     *
     * @param Configurator $configurator Chosen and loaded Phassets configurator.
     */
    public function __construct(Configurator $configurator);

    /**
     * Using an internal algorithm, try to create an unique filename of received assets,
     * writing the changes to $outputAsset::setFilename().
     * Note: There is no need to set the $outputAsset::setOutputExtension().
     *
     * @param Asset[] $assets Array of assets for merging their filenames
     * @param Asset $outputAsset Asset Asset instance whose filenames should be
     *                                 merged according to the internal algorithm.
     * @throws PhassetsInternalException If the merge of filenames cannot be performed
     */
    public function mergeFilenames($assets, Asset $outputAsset);

    /**
     * Using an internal algorithm, try to merge all contents of an array of assets,
     * writing the changes to $outputAsset::setContents().
     *
     * @param Asset[] $assets Array of assets to be merged
     * @param Asset $outputAsset Asset Asset instance whose contents should be
     *                                 modified according to the internal algorithm.
     * @throws PhassetsInternalException If the merge of contents cannot be performed
     */
    public function mergeContents($assets, Asset $outputAsset);
}