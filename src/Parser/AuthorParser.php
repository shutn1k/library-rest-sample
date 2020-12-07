<?php

namespace App\Parser;

use App\Exceptions\JsonDecodeException;
use App\Exceptions\ParserException;

/**
 * Class AuthorParser
 * @package App\Parser
 */
class AuthorParser extends ParserAbstarct {

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

        if (!array_key_exists('name', $array)) {
            throw new ParserException();
        }

        return $array;
    }
}