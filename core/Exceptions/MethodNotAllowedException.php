<?php

declare(strict_types=1);

namespace Core\Exceptions;

final class MethodNotAllowedException extends HttpException
{
    /**
     * Allowed HTTP methods.
     *
     * @var array<int,string>
     */
    private array $allowedMethods;


    /**
     * Create 405 exception.
     *
     * @param array<int,string> $allowedMethods
     */
    public function __construct(
        array $allowedMethods,
        string $message = 'Method not allowed.'
    )
    {
        $this->allowedMethods = array_values(

            array_unique(

                array_map(

                    'strtoupper',

                    $allowedMethods

                )

            )

        );

        parent::__construct(

            405,

            $message,

            [
                'allowed_methods' => $this->allowedMethods
            ]

        );
    }


    /**
     * Return allowed HTTP methods.
     *
     * @return array<int,string>
     */
    public function allowedMethods(): array
    {
        return $this->allowedMethods;
    }
}