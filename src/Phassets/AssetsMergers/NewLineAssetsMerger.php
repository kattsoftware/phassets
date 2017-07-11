<?php

namespace Phassets\AssetsMergers;

use Phassets\Asset;
use Phassets\Exceptions\PhassetsInternalException;
use Phassets\Interfaces\AssetsMerger;
use Phassets\Interfaces\Configurator;

class NewLineAssetsMerger implements AssetsMerger
{
    /**
     * AssetsMerger constructor.
     *
     * @param Configurator $configurator Chosen and loaded Phassets configurator.
     */
    public function __construct(Configurator $configurator)
    {
    }

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
    public function mergeFilenames($assets, Asset $outputAsset)
    {
        $filename = '';

        foreach($assets as $asset) {
            $filename .= $asset->getFilename();
        }

        $outputAsset->setFilename(md5($filename));
    }

    /**
     * Using an internal algorithm, try to merge all contents of an array of assets,
     * writing the changes to $outputAsset::setContents().
     *
     * @param Asset[] $assets Array of assets to be merged
     * @param Asset $outputAsset Asset Asset instance whose contents should be
     *                                 modified according to the internal algorithm.
     * @throws PhassetsInternalException If the merge of contents cannot be performed
     */
    public function mergeContents($assets, Asset $outputAsset)
    {
        $contents = '';

        foreach ($assets as $asset) {
            $contents .= $asset->getContents() . PHP_EOL;
        }

        $outputAsset->setContents($contents);
    }
}