<?php

namespace App\Model\Search;

use App\Constants\Locale;

class BookSearchParams
{
    private string $locale = Locale::DEFAULT_LOCALE;
    private ?string $bookTitle = null;

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return BookSearchParams
     */
    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBookTitle(): ?string
    {
        return $this->bookTitle;
    }

    /**
     * @param string|null $bookTitle
     *
     * @return BookSearchParams
     */
    public function setBookTitle(?string $bookTitle): static
    {
        $this->bookTitle = $bookTitle;

        return $this;
    }
}
