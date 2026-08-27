<?php

declare(strict_types=1);

namespace Core\Routing;
use RuntimeException;

final class RouteCollection
{
    /**
     * Registered routes.
     *
     * @var array<string,array<string,Route>>
     */
    private array $routes = [];


    /**
     * Add route.
     */
    public function add(
        Route $route
    ): self
    {
        $method = $route->method();

        $uri = $route->uri();


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Routes
        |--------------------------------------------------------------------------
        */

        if (isset($this->routes[$method][$uri])) {

            throw new RuntimeException(

                "Route already registered: {$method} {$uri}"

            );

        }


        /*
        |--------------------------------------------------------------------------
        | Register Route
        |--------------------------------------------------------------------------
        */

        $this->routes[$method][$uri] = $route;


        return $this;
    }


    /**
     * Find route for HTTP method and URI.
     */
    public function match(
        string $method,
        string $uri
    ): ?Route
    {
        $method = strtoupper($method);

        $uri = $this->normalize($uri);

        return $this->routes[$method][$uri] ?? null;
    }

    /**
     * Determine whether a URI exists for any HTTP method.
     *
     * Includes both static and parameterized routes.
     */
    public function hasUri(
        string $uri
    ): bool
    {
        $uri = $this->normalize($uri);

        foreach ($this->routes as $routes) {

            /*
            |--------------------------------------------------------------------------
            | Static Route
            |--------------------------------------------------------------------------
            */

            if (isset($routes[$uri])) {

                return true;
            }


            /*
            |--------------------------------------------------------------------------
            | Parameterized Route
            |--------------------------------------------------------------------------
            */

            foreach ($routes as $route) {

                if (!$route->hasParameters()) {

                    continue;
                }


                if ($route->match($uri) !== null) {

                    return true;
                }
            }
        }


        return false;
    }


    /**
     * Return allowed methods for a URI.
     *
     * @return array<int,string>
     */
    public function methodsForUri(
        string $uri
    ): array
    {
        $uri = $this->normalize($uri);

        $methods = [];

        foreach ($this->routes as $method => $routes) {

            if (isset($routes[$uri])) {

                $methods[] = $method;
            }
        }

        sort($methods);

        return $methods;
    }




    /**
     * Return allowed HTTP methods for a URI.
     *
     * Includes both static and parameterized routes.
     *
     * @return array<int,string>
     */
    public function allowedMethods(
        string $uri
    ): array
    {
        $uri = $this->normalize($uri);

        $methods = [];


        foreach ($this->routes as $method => $routes) {

            /*
            |--------------------------------------------------------------------------
            | Static Route
            |--------------------------------------------------------------------------
            */

            if (isset($routes[$uri])) {

                $methods[] = $method;

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Parameterized Route
            |--------------------------------------------------------------------------
            */

            foreach ($routes as $route) {

                if (!$route->hasParameters()) {

                    continue;
                }


                if ($route->match($uri) !== null) {

                    $methods[] = $method;

                    break;
                }
            }
        }


        sort($methods);

        return array_values(array_unique($methods));
    }


    /**
     * Determine if route exists.
     */
    public function has(
        string $method,
        string $uri
    ): bool
    {
        return $this->match(

            $method,

            $uri

        ) !== null;
    }


    /**
     * Return all routes.
     *
     * @return array<string,array<string,Route>>
     */
    public function all(): array
    {
        return $this->routes;
    }


    /**
     * Find route by name.
     */
    public function findByName(
        string $name
    ): ?Route
    {
        foreach ($this->routes as $routes) {

            foreach ($routes as $route) {

                if ($route->getName() === $name) {

                    return $route;

                }

            }

        }

        return null;
    }


    /**
     * Return count of routes.
     */
    public function count(): int
    {
        $count = 0;

        foreach ($this->routes as $routes) {

            $count += count($routes);

        }

        return $count;
    }


    /**
     * Remove all routes.
     *
     * Useful for testing.
     */
    public function clear(): void
    {
        $this->routes = [];
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
         * Match route and return extracted parameters.
         *
         * Static routes are checked first so that:
         *
         * /users/create
         *
         * takes precedence over:
         *
         * /users/{id}
         *
         * @return array{
         *     route: Route,
         *     parameters: array<string,string>
         * }|null
         */
        public function matchWithParameters(
            string $method,
            string $uri
        ): ?array
        {
            $method = strtoupper($method);

            $uri = $this->normalize($uri);


            /*
            |--------------------------------------------------------------------------
            | No Routes For Method
            |--------------------------------------------------------------------------
            */

            if (!isset($this->routes[$method])) {

                return null;

            }


            /*
            |--------------------------------------------------------------------------
            | Static Routes First
            |--------------------------------------------------------------------------
            */

            if (isset($this->routes[$method][$uri])) {

                return [

                    'route' => $this->routes[$method][$uri],

                    'parameters' => []

                ];

            }


            /*
            |--------------------------------------------------------------------------
            | Dynamic Routes
            |--------------------------------------------------------------------------
            */

            foreach ($this->routes[$method] as $route) {

                if (!$route->hasParameters()) {

                    continue;

                }


                $parameters = $route->match($uri);


                if ($parameters !== null) {

                    return [

                        'route' => $route,

                        'parameters' => $parameters

                    ];

                }
            }


            return null;
        }
}