<?php

declare(strict_types=1);

namespace Core\Routing;

final class RouteGroup
{
    /**
     * URI prefix.
     */
    private string $prefix;


    /**
     * Middleware stack.
     *
     * @var array<int,string>
     */
    private array $middleware;


    /**
     * Create route group.
     *
     * @param array<int,string> $middleware
     */
    public function __construct(
        string $prefix = '',
        array $middleware = []
    )
    {
        $this->prefix = $this->normalizePrefix($prefix);

        $this->middleware = $middleware;
    }


    /**
     * Apply prefix to URI.
     */
    public function applyPrefix(
        string $uri
    ): string
    {
        return $this->prefix .

            '/' .

            ltrim($uri, '/');
    }


    /**
     * Return middleware stack.
     *
     * @return array<int,string>
     */
    public function middleware(): array
    {
        return $this->middleware;
    }


    /**
     * Determine if group has middleware.
     */
    public function hasMiddleware(): bool
    {
        return !empty($this->middleware);
    }


    /**
     * Normalize prefix.
     */
    private function normalizePrefix(
        string $prefix
    ): string
    {
        if ($prefix === '') {

            return '';

        }


        return '/' . trim($prefix, '/');
    }
}