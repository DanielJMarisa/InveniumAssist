<?php

declare(strict_types=1);

namespace Core\Routing;

use Closure;
use Core\Container\Container;
use Core\Http\Request;
use RuntimeException;

final class MiddlewarePipeline
{
    /**
     * Dependency injection container.
     */
    private Container $container;


    /**
     * Current request.
     */
    private Request $request;


    /**
     * Middleware stack.
     *
     * @var array<int,string>
     */
    private array $middleware = [];


    /**
     * Final destination.
     */
    private Closure $destination;


    /**
     * Create middleware pipeline.
     */
    public function __construct(
        Container $container
    )
    {
        $this->container = $container;
    }


    /**
     * Set current request.
     */
    public function request(
        Request $request
    ): self
    {
        $this->request = $request;

        return $this;
    }


    /**
     * Register middleware stack.
     *
     * @param array<int,string> $middleware
     */
    public function through(
        array $middleware
    ): self
    {
        $this->middleware = $middleware;

        return $this;
    }


    /**
     * Set final destination.
     */
    public function then(
        Closure $destination
    ): mixed
    {
        $this->destination = $destination;

        return $this->carry(0);
    }

    /**
     * Execute middleware pipeline.
     */
    private function carry(
        int $index
    ): mixed
    {
        /*
        |--------------------------------------------------------------------------
        | Pipeline Destination
        |--------------------------------------------------------------------------
        */

        if ($index >= count($this->middleware)) {

            return ($this->destination)();

        }


        /*
        |--------------------------------------------------------------------------
        | Resolve Middleware Class
        |--------------------------------------------------------------------------
        */

        $middlewareClass = $this->middleware[$index];


        if (!class_exists($middlewareClass)) {

            throw new RuntimeException(

                "Middleware not found: {$middlewareClass}"

            );

        }


        /*
        |--------------------------------------------------------------------------
        | Resolve Through Container
        |--------------------------------------------------------------------------
        */

        $middleware = $this->container->make(

            $middlewareClass

        );


        /*
        |--------------------------------------------------------------------------
        | Validate Middleware
        |--------------------------------------------------------------------------
        */

        if (!method_exists($middleware, 'handle')) {

            throw new RuntimeException(

                "Middleware missing handle method: {$middlewareClass}"

            );

        }


        /*
        |--------------------------------------------------------------------------
        | Execute Middleware
        |--------------------------------------------------------------------------
        */

        return $middleware->handle(

            $this->request,

            fn () => $this->carry(

                $index + 1

            )

        );
    }
}

