<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedChatAccessException extends Exception
{
    protected $message = 'Unauthorized access to chat';
    protected $code = 403;
}
