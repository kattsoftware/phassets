<?php

namespace Phassets\Interfaces;

interface Configurator
{
    /**
     * Returns the config item having a specific name; if that setting
     * is an array, an index may be supplied in order to fetch the exact
     * array element.
     *
     * @param string $name Setting name
     * @param string $index If setting is an array, this can be array's key
     *                      for proper element fetch, otherwise it should be null
     * @return mixed The setting value; otherwise null
     */
    public function getConfig($name, $index = null);
}