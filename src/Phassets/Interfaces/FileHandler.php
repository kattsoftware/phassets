<?php

namespace Phassets\Interfaces;

use Phassets\Asset;

/**
 * FileHandler interface for creating the handlers
 * (binary, resource, plain text etc.) for assets
 *
 * This content is released under the MIT License (MIT).
 * @see LICENSE file
 */
interface FileHandler
{
    /**
     * Sets the Asset instance.
     *
     * @param Asset $asset Asset instance for fetching its contents
     */
    public function setAsset(Asset $asset);

    /**
     * Fetches the contents of an asset. May be the plain
     * file source, an resource (i.e. image handler) etc.
     * Returns null in case of fail.
     *
     * @return mixed Source of the file/a file pointer/resource etc.
     */
    public function getContents();

    /**
     * Sets the updated contents of an asset.
     *
     * @param mixed $contents Modified source of the file/a file pointer/resource etc.
     */
    public function setContents($contents = null);
}