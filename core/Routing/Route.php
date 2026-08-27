<?php

declare(strict_types=1);

namespace Core\Routing;

use RuntimeException;

final class Route
{
    /**
     * HTTP method.
     */
    private string $method;


    /**
     * Original URI pattern.
     */
    private string $uri;


    /**
     * Compiled regular expression.
     */
    private string $pattern;


    /**
     * Route parameter names.
     *
     * @var array<int,string>
     */
    private array $parameters = [];


    /**
     * Route action.
     *
     * @var callable|array
     */
    private mixed $action;


    /**
     * Route middleware stack.
     *
     * @var array<int,string>
     */
    private array $middleware = [];


    /**
     * Route name.
     */
    private ?string $name = null;


    /**
     * Create route.
     */
    public function __construct(
        string $method,
        string $uri,
        callable|array $action
    )
    {
        $this->method = strtoupper($method);

        $this->uri = $this->normalize($uri);

        $this->validateAction($action);

        $this->action = $action;

        $this->compilePattern();
    }


    /**
     * Return HTTP method.
     */
    public function method(): string
    {
        return $this->method;
    }


    /**
     * Return URI.
     */
    public function uri(): string
    {
        return $this->uri;
    }


    /**
     * Return action.
     */
    public function action(): mixed
    {
        return $this->action;
    }


    /**
     * Attach middleware.
     *
     * @param string|array<int,string> $middleware
     */
    public function middleware(
        string|array $middleware
    ): self
    {
        if (is_string($middleware)) {

            $middleware = [

                $middleware

            ];

        }


        foreach ($middleware as $item) {

            $this->middleware[] = $item;

        }


        return $this;
    }


    /**
     * Return middleware.
     *
     * @return array<int,string>
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }


    /**
     * Assign route name.
     */
    public function name(
        string $name
    ): self
    {
        $this->name = $name;

        return $this;
    }


    /**
     * Return route name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }


    /**
     * Determine if route has middleware.
     */
    public function hasMiddleware(): bool
    {
        return !empty($this->middleware);
    }


    /**
     * Determine if route has name.
     */
    public function hasName(): bool
    {
        return $this->name !== null;
    }


    /**
     * Determine if route contains parameters.
     */
    public function hasParameters(): bool
    {
        return !empty($this->parameters);
    }


    /**
     * Return parameter names.
     *
     * @return array<int,string>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }


    /**
     * Match URI against route pattern.
     *
     * @return array<string,string>|null
     */
    public function match(
        string $uri
    ): ?array
    {
        $uri = $this->normalize($uri);

        $matches = [];

        if (
            preg_match(
                $this->pattern,
                $uri,
                $matches
            ) !== 1
        ) {

            return null;

        }


        $parameters = [];

        foreach ($this->parameters as $parameter) {

            if (!array_key_exists($parameter, $matches)) {

                return null;

            }

            $parameters[$parameter] = $matches[$parameter];

        }


        return $parameters;
    }


    /**
     * Compile URI into regular expression.
     */
    private function compilePattern(): void
    {
        $segments = explode(
            '/',
            trim($this->uri, '/')
        );

        $pattern = [];

        foreach ($segments as $segment) {

            /*
            |--------------------------------------------------------------------------
            | Parameter
            |--------------------------------------------------------------------------
            |
            | Supports:
            |
            | {id}
            | {id:\d+}
            |
            */

            if (
                preg_match(
                    '/^\{([A-Za-z_][A-Za-z0-9_]*)\}$/',
                    $segment,
                    $matches
                ) === 1
            ) {

                $parameter = $matches[1];

                if (
                    in_array(
                        $parameter,
                        $this->parameters,
                        true
                    )
                ) {
                    throw new RuntimeException(
                        "Duplicate route parameter: {$parameter}"
                    );
                }

                $this->parameters[] = $parameter;

                $pattern[] =
                    '(?P<'
                    . $parameter
                    . '>[0-9]+)';

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Static Segment
            |--------------------------------------------------------------------------
            */

            $pattern[] = preg_quote(
                $segment,
                '#'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Root Route
        |--------------------------------------------------------------------------
        */

        if (empty($pattern)) {

            $this->pattern = '#^/$#';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Final Pattern
        |--------------------------------------------------------------------------
        */

        $this->pattern =
            '#^/'
            . implode('/', $pattern)
            . '/?$#';
    }


    /**
     * Validate route action.
     */
    private function validateAction(
        callable|array $action
    ): void
    {
        if (is_callable($action)) {

            return;

        }


        if (
            !is_array($action)
            ||
            count($action) !== 2
        ) {

            throw new RuntimeException(

                'Invalid route action.'

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
}