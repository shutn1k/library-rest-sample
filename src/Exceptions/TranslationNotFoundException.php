<?php

namespace App\Exceptions;

use Exception;

/**
 * Class TranslationNotFoundException
 * @package App\Exceptions
 */
class TranslationNotFoundException extends Exception {

    protected $code = 12303;

    protected $message = 'Translation not found';
}