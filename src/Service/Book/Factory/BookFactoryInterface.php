<?php

namespace App\Service\Book\Factory;

use App\Entity\Library\Book;

interface BookFactoryInterface
{
    /**
     * @param mixed $data
     *
     * @return Book
     */
    public function create(mixed $data): Book;
}
