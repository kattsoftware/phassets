<?php

namespace Phassets\Interfaces;

use Phassets\Asset;

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
     * the contents accordingly. If succeeded, return true; false otherwise.
     *
     * @param Asset $asset
     * @return bool Whether the filtering succeeded or not
     */
    public function filter(Asset $asset);
}