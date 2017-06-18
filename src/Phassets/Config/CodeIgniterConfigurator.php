<?php

namespace Phassets\Config;

use Phassets\Interfaces\Configurator;

class CodeIgniterConfigurator implements Configurator
{
    /*
     * The name of /application/config/ configuration file.
     */
    const CONFIG_FILE_NAME = 'phassets';

    /**
     * @var CI_Controller
     */
    private $ci;

    /**
     * CodeIgniterConfigurator constructor.
     */
    public function __construct()
    {
        $this->ci = &get_instance();

        $this->ci->config->load(self::CONFIG_FILE_NAME, false, true);
    }

    /**
     * Returns the config item having a specific name; if that setting
     * is an array, an index may be supplied in order to fetch the exact
     * array element.
     *
     * @param string $name  Setting name
     * @param string $index If setting is an array, this can be array's key
     *                      for direct element fetch, otherwise it should be null
     * @return mixed
     */
    public function getConfig($name, $index = null)
    {
        if($index === null) {
            return $this->ci->config->item($name, 'phassets');
        }

        $setting = $this->ci->config->item($name, 'phassets');

        return isset($setting[$index]) ? $setting[$index] : null;
    }
}