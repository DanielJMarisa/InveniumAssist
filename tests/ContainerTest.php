<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Container\Container;


/*
|--------------------------------------------------------------------------
| Test Dependencies
|--------------------------------------------------------------------------
*/

final class ContainerTestLogger
{
}


final class ContainerTestService
{
    public function __construct(
        public ContainerTestLogger $logger
    ) {
    }
}


final class ContainerTestA
{
    public function __construct(
        ContainerTestB $dependency
    ) {
    }
}


final class ContainerTestB
{
    public function __construct(
        ContainerTestA $dependency
    ) {
    }
}


/*
|--------------------------------------------------------------------------
| Container
|--------------------------------------------------------------------------
*/

$container = new Container();


/*
|--------------------------------------------------------------------------
| 1. Bind Test
|--------------------------------------------------------------------------
*/

$container->bind(

    ContainerTestLogger::class,

    ContainerTestLogger::class

);

$bindOne = $container->make(

    ContainerTestLogger::class

);

$bindTwo = $container->make(

    ContainerTestLogger::class

);

assert(

    $bindOne instanceof ContainerTestLogger,

    'bind() did not resolve the expected class.'

);

assert(

    $bindOne !== $bindTwo,

    'bind() should create a new instance each time.'

);


/*
|--------------------------------------------------------------------------
| 2. Singleton Test
|--------------------------------------------------------------------------
*/

$container->singleton(

    ContainerTestLogger::class,

    ContainerTestLogger::class

);

$singletonOne = $container->make(

    ContainerTestLogger::class

);

$singletonTwo = $container->make(

    ContainerTestLogger::class

);

assert(

    $singletonOne === $singletonTwo,

    'singleton() should return the same instance.'

);


/*
|--------------------------------------------------------------------------
| 3. Instance Test
|--------------------------------------------------------------------------
*/

$existingInstance = new ContainerTestLogger();

$container->instance(

    ContainerTestLogger::class,

    $existingInstance

);

$resolvedInstance = $container->make(

    ContainerTestLogger::class

);

assert(

    $resolvedInstance === $existingInstance,

    'instance() did not return the registered instance.'

);


/*
|--------------------------------------------------------------------------
| 4. Automatic Dependency Resolution
|--------------------------------------------------------------------------
*/

$service = $container->make(

    ContainerTestService::class

);

assert(

    $service instanceof ContainerTestService,

    'Container failed to automatically resolve the service.'

);

assert(

    $service->logger instanceof ContainerTestLogger,

    'Container failed to resolve the constructor dependency.'

);


/*
|--------------------------------------------------------------------------
| 5. Circular Dependency Detection
|--------------------------------------------------------------------------
*/

try {

    $container->make(

        ContainerTestA::class

    );

    throw new RuntimeException(

        'Circular dependency was not detected.'

    );

}

catch (RuntimeException $exception) {

    assert(

        str_contains(

            $exception->getMessage(),

            'Circular dependency detected'

        ),

        'Unexpected circular dependency error.'

    );
}


/*
|--------------------------------------------------------------------------
| 6. Flush Test
|--------------------------------------------------------------------------
*/

$container->flush();

assert(

    !$container->bound(

        ContainerTestLogger::class

    ),

    'flush() did not clear container bindings.'

);


/*
|--------------------------------------------------------------------------
| Success
|--------------------------------------------------------------------------
*/

echo PHP_EOL;

echo 'Container tests passed successfully.' . PHP_EOL;

echo PHP_EOL;

