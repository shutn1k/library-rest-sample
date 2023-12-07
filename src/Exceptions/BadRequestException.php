<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class BadRequestException extends Exception
{
    private array $errors;

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array $errors
     */
    public function __construct(
        string $message = "",
        int $code = 0,
        Throwable|null $previous = null,
        array $errors = []
    ) {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
