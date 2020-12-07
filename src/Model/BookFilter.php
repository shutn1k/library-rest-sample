<?php

namespace App\Model;

/**
 * Class BookFilter
 * @package App\Model
 */
class BookFilter {

    /** ToDo make pagination */
    public const BOOK_LIMIT = 10;

    /** @var string|null */
    private $name;

    /**
     * @return string|null
     */
    public function getName(): ?string {

        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return BookFilter
     */
    public function setName(?string $name): self {

        $this->name = $name;

        return $this;
    }
}