<?php

namespace FileIO\Exceptions;

use Exception;

class ReadOnlyException extends Exception
{
    public function __construct(
        $message = 'Attempted to write to a read only file',
        $code = 0,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
