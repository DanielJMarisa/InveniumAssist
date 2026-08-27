<?php

declare(strict_types=1);

namespace Core\Routing;

use Closure;
use Core\Container\Container;
use Core\Exceptions\MethodNotAllowedException;
use Core\Exceptions\NotFoundException;
use Core\Http\Request;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

final class Router
{
    /**
     * Route collection.
     */
    private RouteCollection $routes;


    /**
     * Dependency injection container.
     */
    private Container $container;


    /**
     * Current route group.
     */
    private ?RouteGroup $group = null;


    /**
     * Create router.
     */
    public function __construct(
        Container $container
    )
    {
        $this->container = $container;

        $this->routes = new RouteCollection();
    }


    /**
     * Register GET route.
     */
    public function get(
        string $uri,
        callable|array $action
    ): self
    {
        return $this->add(
            'GET',
            $uri,
            $action
        );
    }


    /**
     * Register POST route.
     */
    public function post(
        string $uri,
        callable|array $action
    ): self
    {
        return $this->add(
            'POST',
            $uri,
            $action
        );
    }


    /**
     * Register PUT route.
     */
    public function put(
        string $uri,
        callable|array $action
    ): self
    {
        return $this->add(
            'PUT',
            $uri,
            $action
        );
    }


    /**
     * Register PATCH route.
     */
    public function patch(
        string $uri,
        callable|array $action
    ): self
    {
        return $this->add(
            'PATCH',
            $uri,
            $action
        );
    }


    /**
     * Register DELETE route.
     */
    public function delete(
        string $uri,
        callable|array $action
    ): self
    {
        return $this->add(
            'DELETE',
            $uri,
            $action
        );
    }


    /**
     * Register HEAD route.
     */
    public function head(
        string $uri,
        callable|array $action
    ): self
    {
        return $this->add(
            'HEAD',
            $uri,
            $action
        );
    }

    /**
     * Create route group.
     *
     * Parent groups are inherited so nested groups retain:
     * - Parent URI prefixes
     * - Parent middleware
     *
     * @param array<int,string> $middleware
     */
    public function group(
        string $prefix,
        array $middleware,
        Closure $callback
    ): self
    {
        $previousGroup = $this->group;

        /*
        |--------------------------------------------------------------------------
        | Build Nested Group
        |--------------------------------------------------------------------------
        */

        if ($previousGroup !== null) {

            $prefix = $previousGroup->applyPrefix($prefix);

            $middleware = array_merge(

                $previousGroup->middleware(),

                $middleware

            );
        }

        $this->group = new RouteGroup(

            $prefix,

            $middleware

        );

        try {

            $callback($this);

        } finally {

            /*
            |--------------------------------------------------------------------------
            | Restore Previous Group
            |--------------------------------------------------------------------------
            */

            $this->group = $previousGroup;
        }

        return $this;
    }


    /**
     * Add route.
     */
    private function add(
        string $method,
        string $uri,
        callable|array $action
    ): self
    {
        $this->validateAction($action);

        if ($this->group !== null) {

            $uri = $this->group->applyPrefix($uri);

        }

        $route = new Route(

            $method,

            $uri,

            $action

        );

        /*
        |--------------------------------------------------------------------------
        | Apply Group Middleware
        |--------------------------------------------------------------------------
        */

        if ($this->group !== null) {

            $route->middleware(

                $this->group->middleware()

            );

        }

        $this->routes->add($route);

        return $this;
    }


    /**
     * Dispatch request.
     */
    public function dispatch(
        string $method,
        string $uri
    ): mixed
    {
        $method = strtoupper($method);

        $uri = $this->normalize($uri);


        /*
        |--------------------------------------------------------------------------
        | Match Route
        |--------------------------------------------------------------------------
        */

        $matched = $this->routes->matchWithParameters(
            $method,
            $uri
        );


        if ($matched === null) {

            if ($this->routes->hasUri($uri)) {

                throw new MethodNotAllowedException(
                    $this->routes->allowedMethods($uri)
                );
            }


            throw new NotFoundException(
                "Route not found: {$method} {$uri}"
            );
        }


        $route = $matched['route'];

        $routeParameters = $matched['parameters'];


        /*
        |--------------------------------------------------------------------------
        | Route Not Found / Method Not Allowed
        |--------------------------------------------------------------------------
        */

        if ($route === null) {

            /*
            |--------------------------------------------------------------------------
            | URI Does Not Exist
            |--------------------------------------------------------------------------
            */

            if (!$this->routes->hasUri($uri)) {

                throw new NotFoundException(

                    "Route not found: {$method} {$uri}"

                );

            }


            /*
            |--------------------------------------------------------------------------
            | URI Exists But Method Is Not Allowed
            |--------------------------------------------------------------------------
            */

            throw new MethodNotAllowedException(

                $this->routes->allowedMethods($uri)

            );
        }


        /*
        |--------------------------------------------------------------------------
        | Route Middleware
        |--------------------------------------------------------------------------
        */

        $middleware = $route->getMiddleware();


        /*
        |--------------------------------------------------------------------------
        | Execute Route Without Middleware
        |--------------------------------------------------------------------------
        */

        if (empty($middleware)) {

            return $this->execute(

                $route->action(),

                $routeParameters

            );

        }


        /*
        |--------------------------------------------------------------------------
        | Resolve Current Request
        |--------------------------------------------------------------------------
        */

        $request = $this->container->make(

            Request::class

        );


        /*
        |--------------------------------------------------------------------------
        | Execute Middleware Pipeline
        |--------------------------------------------------------------------------
        */

        return $this->container

            ->make(

                MiddlewarePipeline::class

            )

            ->request(

                $request

            )

            ->through(

                $middleware

            )

            ->then(

                function () use ($route, $routeParameters): mixed {

                    return $this->execute(

                        $route->action(),

                        $routeParameters

                    );

                }

            );
    }


        /**
         * Execute route action.
         *
         * @param array<string,string> $routeParameters
         */
        private function execute(
            callable|array $action,
            array $routeParameters = []
        ): mixed
        {
            /*
            |--------------------------------------------------------------------------
            | Direct Callable
            |--------------------------------------------------------------------------
            */

            if (is_callable($action)) {

                return $this->executeCallable(

                    $action,

                    $routeParameters

                );
            }


            /*
            |--------------------------------------------------------------------------
            | Controller Action
            |--------------------------------------------------------------------------
            */

            [$controller, $method] = $action;


            if (!class_exists($controller)) {

                throw new RuntimeException(

                    "Controller not found: {$controller}"

                );
            }


            /*
            |--------------------------------------------------------------------------
            | Resolve Controller Through Container
            |--------------------------------------------------------------------------
            */

            $instance = $this->container->make(

                $controller

            );


            /*
            |--------------------------------------------------------------------------
            | Validate Controller Method
            |--------------------------------------------------------------------------
            */

            if (!method_exists($instance, $method)) {

                throw new RuntimeException(

                    "Controller method not found: {$controller}::{$method}"

                );
            }


            /*
            |--------------------------------------------------------------------------
            | Resolve Method Dependencies
            |--------------------------------------------------------------------------
            */

            $reflection = new ReflectionMethod(

                $instance,

                $method

            );


            $arguments = [];


            foreach ($reflection->getParameters() as $parameter) {

                $arguments[] = $this->resolveMethodParameter(

                    $parameter,

                    $routeParameters,

                    $controller,

                    $method

                );
            }


            /*
            |--------------------------------------------------------------------------
            | Execute Controller Method
            |--------------------------------------------------------------------------
            */

            return $instance->{$method}(

                ...$arguments

            );
        }


    /**
     * Execute a direct callable with route parameters.
     *
     * @param array<string,string> $routeParameters
     */
    private function executeCallable(
        callable $action,
        array $routeParameters
    ): mixed
    {
        $reflection = new \ReflectionFunction(

            \Closure::fromCallable($action)

        );


        $arguments = [];


        foreach ($reflection->getParameters() as $parameter) {

            $arguments[] = $this->resolveMethodParameter(

                $parameter,

                $routeParameters,

                'route callable',

                '__invoke'

            );
        }


        return $action(

            ...$arguments

        );
    }


    /**
     * Resolve controller method parameter.
     *
     * @param array<string,string> $routeParameters
     */
    private function resolveMethodParameter(
        ReflectionParameter $parameter,
        array $routeParameters,
        string $controller,
        string $method
    ): mixed
    {
        $parameterName = $parameter->getName();

        /*
        |--------------------------------------------------------------------------
        | Route Parameter
        |--------------------------------------------------------------------------
        */

        if (array_key_exists($parameterName, $routeParameters)) {

            return $this->castRouteParameter(

                $routeParameters[$parameterName],

                $parameter

            );

        }


        /*
        |--------------------------------------------------------------------------
        | Dependency Type
        |--------------------------------------------------------------------------
        */

        $type = $parameter->getType();


        /*
        |--------------------------------------------------------------------------
        | Untyped Parameter
        |--------------------------------------------------------------------------
        */

        if ($type === null) {

            if ($parameter->isDefaultValueAvailable()) {

                return $parameter->getDefaultValue();

            }

            throw new RuntimeException(

                sprintf(

                    'Unable to resolve parameter $%s in %s::%s().',

                    $parameterName,

                    $controller,

                    $method

                )

            );
        }


        /*
        |--------------------------------------------------------------------------
        | Built-in Parameter
        |--------------------------------------------------------------------------
        */

        if (
            !$type instanceof ReflectionNamedType
            ||
            $type->isBuiltin()
        ) {

            if ($parameter->isDefaultValueAvailable()) {

                return $parameter->getDefaultValue();

            }

            throw new RuntimeException(

                sprintf(

                    'Unable to resolve built-in parameter $%s in %s::%s().',

                    $parameterName,

                    $controller,

                    $method

                )

            );
        }


        /*
        |--------------------------------------------------------------------------
        | Container Dependency
        |--------------------------------------------------------------------------
        */

        return $this->container->make(

            $type->getName()

        );
    }

    /**
     * Cast route parameter to declared scalar type.
     */
    private function castRouteParameter(
        string $value,
        ReflectionParameter $parameter
    ): mixed
    {
        $type = $parameter->getType();


        if (
            !$type instanceof ReflectionNamedType
            ||
            !$type->isBuiltin()
        ) {

            return $value;

        }


        return match ($type->getName()) {

            'string' => $value,

            'int' => filter_var(
                $value,
                FILTER_VALIDATE_INT,
                FILTER_NULL_ON_FAILURE
            ) ?? throw new RuntimeException(

                "Invalid integer route parameter: {$parameter->getName()}"

            ),

            'float' => filter_var(
                $value,
                FILTER_VALIDATE_FLOAT,
                FILTER_NULL_ON_FAILURE
            ) ?? throw new RuntimeException(

                "Invalid float route parameter: {$parameter->getName()}"

            ),

            'bool' => filter_var(
                $value,
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            ) ?? throw new RuntimeException(

                "Invalid boolean route parameter: {$parameter->getName()}"

            ),

            default => $value

        };
    }

    /**
     * Validate route action.
     */
    private function validateAction(
        callable|array $action
    ): void {
        /*
        |--------------------------------------------------------------------------
        | Direct Callable
        |--------------------------------------------------------------------------
        */

        if (is_callable($action)) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Controller Action
        |--------------------------------------------------------------------------
        */

        if (
            !is_array($action)
            ||
            count($action) !== 2
        ) {

            throw new RuntimeException(

                'Invalid route action.'

            );

        }


        [$controller, $method] = $action;


        if (
            !is_string($controller)
            ||
            !is_string($method)
            ||
            $controller === ''
            ||
            $method === ''
        ) {

            throw new RuntimeException(

                'Invalid controller route action.'

            );

        }
    }


    /**
     * Normalize URI.
     */
    private function normalize(
        string $uri
    ): string
    {
        $uri = '/' . trim($uri, '/');

        return $uri === '//'

            ? '/'

            : $uri;
    }


    /**
     * Return registered routes.
     *
     * @return array<string,array<string,Route>>
     */
    public function routes(): array
    {
        return $this->routes->all();
    }
}