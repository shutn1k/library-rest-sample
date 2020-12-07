<?php

namespace App\Parser;

use App\Entity\Book;
use App\Exceptions\JsonDecodeException;
use App\Exceptions\ParserException;

/**
 * Class BookParser
 * @package App\Parser
 */
class BookParser extends ParserAbstarct {

    /** @var Book */
    protected $model;

    /**
     * @return array
     * @throws JsonDecodeException
     * @throws ParserException
     */
    public function parse(): array {

        $array = json_decode($this->data, true);
        if ($array === null) {
            throw new JsonDecodeException();
        }

        // Можно валидировать по json-schema
        if (
            !array_key_exists('name', $array) ||
            !is_array($array['name']) ||
            !array_key_exists('ru', $array['name']) ||
            !array_key_exists('en', $array['name']) ||
            !array_key_exists('authors', $array) ||
            !is_array($array['authors'])
        ) {
            throw new ParserException();
        }

        return $array;
    }
}