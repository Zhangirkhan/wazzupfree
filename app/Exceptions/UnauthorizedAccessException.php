<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedAccessException extends Exception
{
    protected string $action;
    protected string $resource;
    protected array $context;

    public function __construct(
        string $action = 'access',
        string $resource = 'resource',
        array $context = [],
        string $message = null,
        int $code = 403,
        Exception $previous = null
    ) {
        $message = $message ?? "Unauthorized to {$action} {$resource}";
        
        parent::__construct($message, $code, $previous);
        $this->action = $action;
        $this->resource = $resource;
        $this->context = $context;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function toArray(): array
    {
        return [
            'action' => $this->action,
            'resource' => $this->resource,
            'context' => $this->context,
            'message' => $this->getMessage()
        ];
    }
}
