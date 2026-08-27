<?php

declare(strict_types=1);

namespace Core\Kernel;

use Core\Container\Container;
use Core\Exceptions\Handler;
use Core\Http\Request;
use Core\Routing\MiddlewarePipeline;
use Core\Routing\Router;
use RuntimeException;
use Throwable;

final class Application
{
    /**
     * Router instance.
     */
    private Router $router;

    /**
     * Dependency injection container.
     */
    private Container $container;

    /**
     * Application middleware.
     *
     * @var array<int,string>
     */
    private array $middleware = [];


    /**
     * Create application.
     */
    public function __construct()
    {
        $this->container = new Container();

        /*
        |--------------------------------------------------------------------------
        | Register Application
        |--------------------------------------------------------------------------
        */

        $this->container->instance(
            self::class,
            $this
        );

        /*
        |--------------------------------------------------------------------------
        | Register Router
        |--------------------------------------------------------------------------
        */

        $this->router = $this->container->make(
            Router::class
        );

        $this->container->instance(
            Router::class,
            $this->router
        );

        /*
        |--------------------------------------------------------------------------
        | Register Global Exception Handler
        |--------------------------------------------------------------------------
        */

        Handler::register();
    }


    /**
     * Return dependency injection container.
     */
    public function container(): Container
    {
        return $this->container;
    }


    /**
     * Return router instance.
     */
    public function router(): Router
    {
        return $this->router;
    }


    /**
     * Register global middleware.
     *
     * @param array<int,string> $middleware
     */
    public function middleware(
        array $middleware
    ): self {
        $this->middleware = array_values(
            $middleware
        );

        return $this;
    }


    /**
     * Load route file.
     */
    public function routes(
        string $file
    ): self {
        if (!is_file($file)) {
            throw new RuntimeException(
                "Route file not found: {$file}"
            );
        }

        $loader = require $file;

        if (!is_callable($loader)) {
            throw new RuntimeException(
                "Route file must return a callable: {$file}"
            );
        }

        $loader($this);

        return $this;
    }


    /**
     * Run application.
     */
    public function run(): mixed
    {
        try {
            /*
            |--------------------------------------------------------------------------
            | Create Current Request
            |--------------------------------------------------------------------------
            */

            $request = new Request();

            /*
            |--------------------------------------------------------------------------
            | Register Current Request
            |--------------------------------------------------------------------------
            |
            | This allows controllers, middleware and other services to receive
            | the current Request instance through the container.
            |
            */

            $this->container->instance(
                Request::class,
                $request
            );

            /*
            |--------------------------------------------------------------------------
            | Execute Middleware Pipeline
            |--------------------------------------------------------------------------
            */

            return $this->container
                ->make(MiddlewarePipeline::class)
                ->request($request)
                ->through($this->middleware)
                ->then(
                    fn (): mixed => $this->router->dispatch(
                        $request->method(),
                        $request->path()
                    )
                );
        } catch (Throwable $exception) {
            /*
            |--------------------------------------------------------------------------
            | Central Exception Handling
            |--------------------------------------------------------------------------
            */

            return Handler::handle(
                $exception
            );
        }
    }
}
