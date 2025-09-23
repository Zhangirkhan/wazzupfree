<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Validator;

class ValidationException extends Exception
{
    protected array $errors;
    protected array $data;

    public function __construct(
        array $errors,
        array $data = [],
        string $message = 'Validation failed',
        int $code = 422,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
        $this->data = $data;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'errors' => $this->errors,
            'data' => $this->data
        ];
    }

    public static function fromValidator(Validator $validator, array $data = []): self
    {
        return new self(
            $validator->errors()->toArray(),
            $data,
            'Validation failed'
        );
    }
}
