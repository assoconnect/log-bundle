<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Exception;

class UnsupportObjectException extends \DomainException
{
    public function __construct($object, $code = 0, \Throwable $previous = null)
    {
        $message = sprintf('Unhandled object of class %s', get_class($object));
        parent::__construct($message, $code, $previous);
    }
}
