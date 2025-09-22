<?php

namespace App\Exceptions;

use Exception;

class ChatNotFoundException extends Exception
{
    protected $message = 'Chat not found';
    protected $code = 404;
}
