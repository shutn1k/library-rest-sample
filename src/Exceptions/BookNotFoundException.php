<?php

namespace App\Exceptions;

use Exception;

/**
 * Class BookNotFoundException
 * @package App\Exceptions
 */
class BookNotFoundException extends Exception {

    protected $code = 12303;

    protected $message = 'Book not found';
}