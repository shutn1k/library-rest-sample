<?php

namespace App\Exceptions;

use Exception;

/**
 * Class AuthorNotFoundException
 * @package App\Exceptions
 */
class AuthorNotFoundException extends Exception {

    protected $code = 12303;

    protected $message = 'Author not found';
}