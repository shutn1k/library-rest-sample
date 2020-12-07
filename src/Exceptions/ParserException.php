<?php

namespace App\Exceptions;

use Exception;

/**
 * Class ParserExeption
 * @package App\Exeptions
 */
class ParserException extends Exception {

    protected $code = 12302;

    protected $message = 'Bad format';
}