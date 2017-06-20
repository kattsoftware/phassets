<?php

namespace Phassets\Loggers;

use Phassets\Interfaces\Configurator;
use Phassets\Interfaces\Logger;

class DummyLogger implements Logger
{
    /**
     * Logger constructor.
     *
     * @param Configurator $configurator Chosen and loaded Phassets configurator.
     */
    public function __construct(Configurator $configurator) {}

    /**
     * Log an error message.
     *
     * @param string $message Message to be logged
     * @return bool Whether the operation succeeded or not
     */
    public function error($message)
    {
        return true;
    }

    /**
     * Log an warning message.
     *
     * @param string $message Message to be logged
     * @return bool Whether the operation succeeded or not
     */
    public function warning($message)
    {
        return true;
    }

    /**
     * Log a debugging message.
     *
     * @param string $message Message to be logged
     * @return bool Whether the operation succeeded or not
     */
    public function debug($message)
    {
        return true;
    }

    /**
     * Log an info message.
     *
     * @param string $message Message to be logged
     * @return bool Whether the operation succeeded or not
     */
    public function info($message)
    {
        return true;
    }
}