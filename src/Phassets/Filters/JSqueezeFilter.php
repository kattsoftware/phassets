<?php

namespace Phassets\Filters;

use Phassets\Asset;
use Phassets\Interfaces\Configurator;
use Phassets\Interfaces\Filter;
use Patchwork\JSqueeze;

class JSqueezeFilter implements Filter
{
    /**
     * Filter constructor.
     *
     * @param Configurator $configurator Chosen and loaded Phassets configurator.
     */
    public function __construct(Configurator $configurator)
    {
        $this->jsqueeze = new JSqueeze();
    }

    /**
     * Process the Asset received and using Asset::setContents(), update
     * the contents accordingly. If succeeded, return true; false otherwise.
     *
     * @param Asset $asset
     * @return bool Whether the filtering succeeded or not
     */
    public function filter(Asset $asset)
    {
        $asset->setContents($this->jsqueeze->squeeze($asset->getContents()));

        return true;
    }
}