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
        try {
            $cssParser = new Parser($asset->getContents());

            $result = $cssParser->parse()->render(OutputFormat::createCompact());
        } catch(SourceException $e) {
            throw new PhassetsInternalException(
                'CssCompactFilter: SourceException caught',
                $e->getCode(),
                $e
            );
        } catch(UnexpectedTokenException $e) {
            throw new PhassetsInternalException(
                'CssCompactFilter: UnexpectedTokenException caught',
                $e->getCode(),
                $e
            );
        }

        $asset->setContents($result);
    }
}