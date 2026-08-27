<?php

declare(strict_types=1);

namespace Core\Container;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use RuntimeException;

final class Container
{
    /**
     * Registered bindings.
     *
     * @var array<string,callable|string>
     */
    private array $bindings = [];


    /**
     * Registered singleton bindings.
     *
     * @var array<string,callable|string>
     */
    private array $singletons = [];


    /**
     * Resolved singleton instances.
     *
     * @var array<string,mixed>
     */
    private array $instances = [];


    /**
     * Classes currently being resolved.
     *
     * Used to detect circular dependencies.
     *
     * @var array<int,string>
     */
    private array $resolving = [];


    /**
     * Register a transient binding.
     *
     * A new instance is resolved each time.
     *
     * @param callable|string $concrete
     */
    public function bind(
        string $abstract,
        callable|string $concrete
    ): void
    {
        $this->bindings[$abstract] = $concrete;

        unset(

            $this->singletons[$abstract],

            $this->instances[$abstract]

        );
    }


    /**
     * Register a singleton binding.
     *
     * The resolved instance is reused.
     *
     * @param callable|string $concrete
     */
    public function singleton(
        string $abstract,
        callable|string $concrete
    ): void
    {
        $this->singletons[$abstract] = $concrete;

        unset(

            $this->bindings[$abstract]

        );
    }


    /**
     * Register an existing instance.
     */
    public function instance(
        string $abstract,
        mixed $instance
    ): void
    {
        $this->instances[$abstract] = $instance;

        unset(

            $this->bindings[$abstract],

            $this->singletons[$abstract]

        );
    }


    /**
     * Determine whether a binding exists.
     */
    public function bound(
        string $abstract
    ): bool
    {
        return isset($this->bindings[$abstract])

            || isset($this->singletons[$abstract])

            || array_key_exists(

                $abstract,

                $this->instances

            );
    }


    /**
     * Resolve a service.
     *
     * @throws RuntimeException
     */
    public function make(
        string $abstract
    ): mixed
    {
        /*
        |--------------------------------------------------------------------------
        | Existing Instance
        |--------------------------------------------------------------------------
        */

        if (array_key_exists(

            $abstract,

            $this->instances

        )) {

            return $this->instances[$abstract];

        }


        /*
        |--------------------------------------------------------------------------
        | Singleton
        |--------------------------------------------------------------------------
        */

        if (isset($this->singletons[$abstract])) {

            $instance = $this->resolveConcrete(

                $abstract,

                $this->singletons[$abstract]

            );

            $this->instances[$abstract] = $instance;

            return $instance;

        }


        /*
        |--------------------------------------------------------------------------
        | Transient Binding
        |--------------------------------------------------------------------------
        */

        if (isset($this->bindings[$abstract])) {

            return $this->resolveConcrete(

                $abstract,

                $this->bindings[$abstract]

            );

        }


        /*
        |--------------------------------------------------------------------------
        | Automatic Resolution
        |--------------------------------------------------------------------------
        */

        return $this->build($abstract);
    }


    /**
     * Resolve a concrete implementation.
     *
     * @param callable|string $concrete
     */
    private function resolveConcrete(
        string $abstract,
        callable|string $concrete
    ): mixed
    {
        if ($concrete instanceof Closure) {

            return $concrete($this);

        }


        if (is_callable($concrete)) {

            return $concrete($this);

        }


        return $this->build($concrete);
    }


    /**
     * Automatically construct a class.
     *
     * @throws RuntimeException
     */
    private function build(
        string $class
    ): mixed
    {
        /*
        |--------------------------------------------------------------------------
        | Circular Dependency Detection
        |--------------------------------------------------------------------------
        */

        if (in_array(

            $class,

            $this->resolving,

            true

        )) {

            $chain = implode(

                ' -> ',

                [

                    ...$this->resolving,

                    $class

                ]

            );

            throw new RuntimeException(

                "Circular dependency detected: {$chain}"

            );
        }


        $this->resolving[] = $class;


        try {

            /*
            |--------------------------------------------------------------------------
            | Verify Class
            |--------------------------------------------------------------------------
            */

            if (!class_exists($class)) {

                throw new RuntimeException(

                    "Container cannot resolve class: {$class}"

                );

            }


            $reflection = new ReflectionClass($class);


            /*
            |--------------------------------------------------------------------------
            | Abstract Classes
            |--------------------------------------------------------------------------
            */

            if (!$reflection->isInstantiable()) {

                throw new RuntimeException(

                    "Container cannot instantiate: {$class}"

                );

            }


            /*
            |--------------------------------------------------------------------------
            | Constructor
            |--------------------------------------------------------------------------
            */

            $constructor = $reflection->getConstructor();


            if ($constructor === null) {

                return $reflection->newInstance();

            }


            /*
            |--------------------------------------------------------------------------
            | Resolve Dependencies
            |--------------------------------------------------------------------------
            */

            $dependencies = [];


            foreach ($constructor->getParameters() as $parameter) {

                $dependencies[] = $this->resolveParameter(

                    $parameter,

                    $class

                );

            }


            return $reflection->newInstanceArgs(

                $dependencies

            );

        }

        catch (ReflectionException $exception) {

            throw new RuntimeException(

                "Unable to resolve class: {$class}",

                0,

                $exception

            );

        }

        finally {

            array_pop($this->resolving);

        }
    }


    /**
     * Resolve constructor parameter.
     *
     * @throws RuntimeException
     */
    private function resolveParameter(
        ReflectionParameter $parameter,
        string $class
    ): mixed
    {
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

                    'Unable to resolve parameter $%s in %s.',

                    $parameter->getName(),

                    $class

                )

            );

        }


        /*
        |--------------------------------------------------------------------------
        | Built-in Types
        |--------------------------------------------------------------------------
        */

        if (!$type instanceof \ReflectionNamedType || $type->isBuiltin()) {

            if ($parameter->isDefaultValueAvailable()) {

                return $parameter->getDefaultValue();

            }


            throw new RuntimeException(

                sprintf(

                    'Unable to resolve parameter $%s in %s.',

                    $parameter->getName(),

                    $class

                )

            );

        }


        return $this->make(

            $type->getName()

        );
    }


    /**
     * Forget a resolved singleton instance.
     */
    public function forget(
        string $abstract
    ): void
    {
        unset(

            $this->instances[$abstract]

        );
    }


    /**
     * Clear the container.
     *
     * Primarily useful for testing.
     */
    public function flush(): void
    {
        $this->bindings = [];

        $this->singletons = [];

        $this->instances = [];

        $this->resolving = [];
    }
}

