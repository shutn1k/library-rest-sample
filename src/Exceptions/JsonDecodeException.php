<?php

namespace App\Exceptions;

use Exception;

/**
 * Class JsonDecodeExeption
 * @package App\Exeptions
 */
class JsonDecodeException extends Exception {

    protected $code = 12301;

    protected $message = 'Bad json';
}