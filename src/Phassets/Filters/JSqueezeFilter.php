<?php

namespace Phassets\Filters;

use Phassets\Asset;
use Phassets\Exceptions\PhassetsInternalException;
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
        if ($asset->getOutputExtension() !== 'js') {
            throw new PhassetsInternalException('Only .js files can be filtered by ' . __CLASS__);
        }

        $asset->setContents((new JSqueeze())->squeeze($asset->getContents()));
    }

    /**
     * Sets the Asset's outputExtension property (Asset::setOutputExtension()).
     * E.g. if you are minifying JS code, then you should set 'js'.
     * If you are compiling SCSS to CSS, then you should set 'css'.
     *
     * @param Asset $asset The instance of asset, having only the fullPath property set
     */
    public function setOutputExtension(Asset $asset)
    {
        $asset->setOutputExtension('js');
    }
}