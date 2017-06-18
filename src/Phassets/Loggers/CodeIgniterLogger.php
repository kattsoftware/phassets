<?php

namespace Phassets\Loggers;

use Phassets\Interfaces\Configurator;
use Phassets\Interfaces\Logger;

class CodeIgniterLogger implements Logger
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
        log_message('ERROR', $message);

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
        log_message('ERROR', '[WARNING] ' . $message);

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
        log_message('DEBUG', $message);

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
        log_message('INFO', $message);

        return true;
    }
}