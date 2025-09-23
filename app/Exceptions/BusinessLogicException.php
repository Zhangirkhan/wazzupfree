<?php

namespace App\Exceptions;

use Exception;

class BusinessLogicException extends Exception
{
    protected string $errorCode;
    protected array $context;

    public function __construct(
        string $message = 'Business logic error',
        string $errorCode = 'BUSINESS_ERROR',
        array $context = [],
        int $code = 400,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
        $this->context = $context;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function toArray(): array
    {
        return [
            'error_code' => $this->errorCode,
            'message' => $this->getMessage(),
            'context' => $this->context,
            'file' => $this->getFile(),
            'line' => $this->getLine()
        ];
    }
}
