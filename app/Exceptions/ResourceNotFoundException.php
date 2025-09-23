<?php

namespace App\Exceptions;

use Exception;

class ResourceNotFoundException extends Exception
{
    protected string $resourceType;
    protected mixed $resourceId;

    public function __construct(
        string $resourceType = 'Resource',
        mixed $resourceId = null,
        string $message = null,
        int $code = 404,
        Exception $previous = null
    ) {
        $message = $message ?? "{$resourceType} not found" . ($resourceId ? " with ID: {$resourceId}" : '');
        
        parent::__construct($message, $code, $previous);
        $this->resourceType = $resourceType;
        $this->resourceId = $resourceId;
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function getResourceId(): mixed
    {
        return $this->resourceId;
    }

    public function toArray(): array
    {
        return [
            'resource_type' => $this->resourceType,
            'resource_id' => $this->resourceId,
            'message' => $this->getMessage()
        ];
    }
}
