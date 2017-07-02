<?php

namespace Phassets\Filters;

use Phassets\Asset;
use Phassets\Interfaces\Configurator;
use Phassets\Interfaces\Filter;
use Patchwork\JSqueeze;

/**
 * Patchwork's JSqueeze wrapper for Phassets
 *
 * This content is released under the MIT License (MIT).
 * @see LICENSE file
 * @see https://github.com/tchwork/jsqueeze For licensing information about JSqueeze
 */
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
     * the contents accordingly. If it fails, will throw PhassetsInternalException
     *
     * @param Asset $asset Asset instance which will be updated via setContents()
     * @throws PhassetsInternalException in case of failure
     */
    public function filter(Asset $asset)
    {
        $asset->setContents($this->jsqueeze->squeeze($asset->getContents()));
    }
}