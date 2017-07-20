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

    /**
     * Sets the Asset's outputExtension property (Asset::setOutputExtension()).
     * E.g. if you are minifying JS code, then you should set 'js'.
     * If you are compiling SCSS to CSS, then you should set 'css'.
     *
     * @param Asset $asset The instance of asset, having only the fullPath property set
     */
    public function setOutputExtension(Asset $asset);
}