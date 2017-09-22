<?php

namespace WebComplete\rbac\exception;

use Throwable;

class RbacException extends \Exception
{

    public function __construct($message, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
