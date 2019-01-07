<?php

namespace Woody\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Woody\Http\Message\Response;

/**
 * Class Dispatcher
 *
 * @package Woody\Http\Server\Middleware
 */
class Dispatcher implements RequestHandlerInterface
{

    /**
     * @var \Woody\Http\Server\Middleware\MiddlewareInterface[]|callable[]
     */
    protected $middlewareStack;

    /**
     * @var int
     */
    protected $index;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * Dispatcher constructor.
     */
    public function __construct()
    {
        $this->middlewareStack = [];
        $this->index = 0;
        $this->debug = false;
    }

    /**
     * @param bool $status
     */
    public function enableDebug($status = null): void
    {
        $this->debug = (is_null($status) || $status === true);
    }

    /**
     * @param \Woody\Http\Server\Middleware\MiddlewareInterface|callable $middleware
     */
    public function pipe($middleware): void
    {
        if ($middleware instanceof MiddlewareInterface && $middleware->isEnabled($this->debug)) {
            $this->middlewareStack[] = $middleware;
        } elseif (is_callable($middleware)) {
            $this->middlewareStack[] = $middleware;
        } else {
            throw new \InvalidArgumentException('Middleware not supported');
        }
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->index = 0;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($middleware = $this->getMiddleware()) {
            $this->index++;

            if ($middleware instanceof MiddlewareInterface) {
                return $middleware->process($request, $this);
            } elseif (is_callable($middleware)) {
                return call_user_func($middleware, $request, $this);
            }
        }

        // Return default value.
        return $this->getDefaultResponse();
    }

    /**
     * @return \Woody\Http\Server\Middleware\MiddlewareInterface|callable|null
     */
    protected function getMiddleware()
    {
        if (isset($this->middlewareStack[$this->index])) {
            return $this->middlewareStack[$this->index];
        }

        return null;
    }

    /**
     * @return \GuzzleHttp\Psr7\Response
     */
    protected function getDefaultResponse(): Response
    {
        return new Response(204);
    }
}
