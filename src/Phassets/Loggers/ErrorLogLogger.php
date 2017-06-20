<?php

namespace Phassets\Loggers;

use Phassets\Interfaces\Configurator;
use Phassets\Interfaces\Logger;

class ErrorLogLogger implements Logger
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
        return error_log('[ERROR] ' . $message);
    }

    /**
     * Log an warning message.
     *
     * @param string $message Message to be logged
     * @return bool Whether the operation succeeded or not
     */
    public function warning($message)
    {
        return error_log('[WARNING] ' . $message);
    }

    /**
     * Log a debugging message.
     *
     * @param string $message Message to be logged
     * @return bool Whether the operation succeeded or not
     */
    public function debug($message)
    {
        return error_log('[DEBUG] ' . $message);
    }

    /**
     * Log an info message.
     *
     * @param string $message Message to be logged
     * @return bool Whether the operation succeeded or not
     */
    public function info($message)
    {
        return error_log('[INFO] ' . $message);
    }
}