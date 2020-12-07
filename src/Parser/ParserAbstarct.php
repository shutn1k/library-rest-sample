<?php

namespace App\Parser;

/**
 * Class ParserAbstarct
 * @package App\Parser
 */
abstract class ParserAbstarct {

    protected $data;

    /**
     * ParserAbstarct constructor.
     *
     * @param $data
     */
    public function __construct($data) {

        $this->data = $data;
    }

    /**
     * Must return model
     *
     * @return mixed
     */
    abstract public function parse();
}