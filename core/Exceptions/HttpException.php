<?php

declare(strict_types=1);

namespace Core\Exceptions;

use Exception;

class HttpException extends Exception
{
    /**
     * HTTP status code.
     */
    protected int $status;


    /**
     * Additional exception data.
     *
     * @var array<string,mixed>
     */
    protected array $data = [];


    /**
     * Create HTTP exception.
     *
     * @param array<string,mixed> $data
     */
    public function __construct(
        int $status = 500,
        string $message = '',
        array $data = [],
        ?\Throwable $previous = null
    )
    {
        parent::__construct(
            $message,
            0,
            $previous
        );

        $this->status = $status;

        $this->data = $data;
    }


    /**
     * Return HTTP status code.
     */
    public function status(): int
    {
        return $this->status;
    }


    /**
     * Return extra exception data.
     *
     * @return array<string,mixed>
     */
    public function data(): array
    {
        return $this->data;
    }
}