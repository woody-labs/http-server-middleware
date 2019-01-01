<?php

namespace Woody\Http\Server\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class Dispatcher
 *
 * @package Woody\Http\Server\Middleware
 */
class Dispatcher implements RequestHandlerInterface
{

    /**
     * @var \Psr\Http\Server\MiddlewareInterface[]
     */
    protected $middlewareStack;

    /**
     * @var int
     */
    protected $index;

    /**
     * Dispatcher constructor.
     */
    public function __construct()
    {
        $this->middlewareStack = [];
        $this->index = 0;
    }

    /**
     * @param \Psr\Http\Server\MiddlewareInterface $middleware
     */
    public function pipe(MiddlewareInterface $middleware): void
    {
        $this->middlewareStack[] = $middleware;
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

            return $middleware->process($request, $this);
        }

        // Return default value.
        return $this->getDefaultResponse();
    }

    /**
     * @return \Psr\Http\Server\MiddlewareInterface|null
     */
    protected function getMiddleware(): MiddlewareInterface
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
        return new Response();
    }
}
