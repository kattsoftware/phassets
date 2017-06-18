<?php

namespace Phassets\Interfaces;

interface Logger
{
    /**
     * Logger constructor.
     *
     * @param Configurator $configurator Chosen and loaded Phassets configurator.
     */
    public function __construct(Configurator $configurator);

    /**
     * Log an error message.
     *
     * @param string $message Message to be logged
     * @return bool Whether the operation succeeded or not
     */
    public function error($message);

    /**
     * Log an warning message.
     *
     * @param string $message Message to be logged
     * @return bool Whether the operation succeeded or not
     */
    public function warning($message);

    /**
     * Log a debugging message.
     *
     * @param string $message Message to be logged
     * @return bool Whether the operation succeeded or not
     */
    public function debug($message);

    /**
     * Log an info message.
     *
     * @param string $message Message to be logged
     * @return bool Whether the operation succeeded or not
     */
    public function info($message);
}