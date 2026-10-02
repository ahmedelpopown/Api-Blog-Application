<?php

namespace App\Exceptions;

use RuntimeException;

class PostsServiceUnavailableException extends RuntimeException
{
    public function __construct(
        string $message = 'Posts service is unavailable.'
    ) {
        parent::__construct($message);
    }
}