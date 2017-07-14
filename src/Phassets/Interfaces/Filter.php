<?php

namespace Phassets\Interfaces;

use Phassets\Asset;
use Phassets\Exceptions\PhassetsInternalException;

/**
 * Filter interface for defining filters components
 *
 * This content is released under the MIT License (MIT).
 * @see LICENSE file
 */
interface Filter
{
    /**
     * Filter constructor.
     *
     * @param Configurator $configurator Chosen and loaded Phassets configurator.
     */
    public function __construct(Configurator $configurator);

    /**
     * Process the Asset received and using Asset::setContents(), update
     * the contents accordingly. If it fails, will throw PhassetsInternalException
     *
     * @param Asset $asset Asset instance which will be updated via setContents()
     * @throws PhassetsInternalException in case of failure
     */
    public function filter(Asset $asset);

//    public function getExtension() /** @return string */
}