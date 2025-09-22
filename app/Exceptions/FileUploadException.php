<?php

namespace App\Exceptions;

use Exception;

class FileUploadException extends Exception
{
    protected $message = 'File upload failed';
    protected $code = 422;
}
