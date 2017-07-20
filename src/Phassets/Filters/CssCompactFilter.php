<?php

namespace Phassets\Filters;

use Phassets\Asset;
use Phassets\Exceptions\PhassetsInternalException;
use Phassets\Interfaces\Configurator;
use Phassets\Interfaces\Filter;
use Sabberworm\CSS\Parsing\SourceException;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\OutputFormat;

/**
 * Sabberworm's CSS PHP parser wrapper for Phassets
 *
 * This content is released under the MIT License (MIT).
 * @see LICENSE file
 * @see https://github.com/sabberworm/PHP-CSS-Parser For licensing information about PHP CSS parser
 */
class CssCompactFilter implements Filter
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
        if ($asset->getOutputExtension() !== 'css') {
            throw new PhassetsInternalException('Only .css files can be filtered by ' . __CLASS__);
        }

        try {
            $cssParser = new Parser($asset->getContents());

            $result = $cssParser->parse()->render(OutputFormat::createCompact());
        } catch (UnexpectedTokenException $e) {
            throw new PhassetsInternalException(
                __CLASS__ . ': UnexpectedTokenException caught',
                $e->getCode(),
                $e
            );
        } catch (SourceException $e) {
            throw new PhassetsInternalException(
                __CLASS__ . ': SourceException caught',
                $e->getCode(),
                $e
            );
        }

        $asset->setContents($result);
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
        $asset->setOutputExtension('css');
    }
}