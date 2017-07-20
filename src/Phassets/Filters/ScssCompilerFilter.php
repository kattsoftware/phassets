<?php

namespace Phassets\Filters;

use Leafo\ScssPhp\Compiler;
use Phassets\Asset;
use Phassets\Exceptions\PhassetsInternalException;
use Phassets\Interfaces\Configurator;
use Phassets\Interfaces\Filter;

/**
 * Leafo's ScssPhp Compiler wrapper for Phassets
 *
 * This content is released under the MIT License (MIT).
 * @see LICENSE file
 * @see http://leafo.github.io/scssphp For licensing information about ScssPhp Compiler
 */
class ScssCompilerFilter implements Filter
{
    /**
     * @var Configurator
     */
    private $configurator;

    /**
     * Filter constructor.
     *
     * @param Configurator $configurator Chosen and loaded Phassets configurator.
     */
    public function __construct(Configurator $configurator)
    {
        $this->configurator = $configurator;
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
        if ($asset->getOutputExtension() !== 'scss') {
            throw new PhassetsInternalException('Only .scss files can be filtered by ' . __CLASS__);
        }

        $scssCompiler = new Compiler();

        // Custom import paths?
        $importPaths = $this->configurator->getConfig('scsscompiler_filter', 'import_paths');

        if ($importPaths) {
            $scssCompiler->setImportPaths($importPaths);
        }

        // Custom vars?
        $customVars = $this->configurator->getConfig('scsscompiler_filter', 'custom_vars');

        if ($customVars) {
            $scssCompiler->setVariables($customVars);
        }

        // Formatter
        $formatter = $this->configurator->getConfig('scsscompiler_filter', 'formatter');

        switch ($formatter) {
            case 'expanded':
                $scssCompiler->setFormatter(\Leafo\ScssPhp\Formatter\Expanded::class);
                break;
            case 'nested':
                $scssCompiler->setFormatter(\Leafo\ScssPhp\Formatter\Nested::class);
                break;
            case 'compressed':
                $scssCompiler->setFormatter(\Leafo\ScssPhp\Formatter\Compressed::class);
                break;
            case 'compact':
                $scssCompiler->setFormatter(\Leafo\ScssPhp\Formatter\Compact::class);
                break;
            default:
            case 'crunched':
                $scssCompiler->setFormatter(\Leafo\ScssPhp\Formatter\Crunched::class);
        }

        $asset->setContents($scssCompiler->compile($asset->getContents()));
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