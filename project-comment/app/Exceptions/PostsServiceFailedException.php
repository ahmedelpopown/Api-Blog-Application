<?php

namespace App\Exceptions;

use RuntimeException;

class PostsServiceFailedException extends RuntimeException
{
    public function __construct(
        string $message = 'Posts service returned an error.'
    ) {
        parent::__construct($message);
    }
}