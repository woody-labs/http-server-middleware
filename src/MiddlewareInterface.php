<?php

namespace Woody\Http\Server\Middleware;

/**
 * Interface MiddlewareInterface
 *
 * @package Woody\Http\Server\Middleware
 */
interface MiddlewareInterface extends \Psr\Http\Server\MiddlewareInterface
{

    /**
     * @param bool $debug
     *
     * @return bool
     */
    public function isEnabled(bool $debug): bool;
}
