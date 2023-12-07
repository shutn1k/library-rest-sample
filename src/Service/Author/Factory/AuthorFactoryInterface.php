<?php

namespace App\Service\Author\Factory;

use App\Entity\Library\Author;
use Symfony\Component\HttpFoundation\Request;

interface AuthorFactoryInterface
{
    /**
     * @param mixed $data
     *
     * @return Author
     */
    public function create(mixed $data): Author;
}
