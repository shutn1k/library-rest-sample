<?php

namespace App\Service\Book;

use App\Constants\Locale;
use App\Entity\Library\Book;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\NotFoundException;
use App\Model\Search\BookSearchParams;
use App\Repository\Library\BookRepository;
use App\Service\Translation\TranslationService;
use Doctrine\ORM\EntityManagerInterface;

class BookService
{
    /**
     * @param BookRepository $bookRepository
     * @param TranslationService $translationService
     */
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly TranslationService $translationService
    ) {
    }

    /**
     * @param Book $book
     *
     * @return void
     *
     * @throws AlreadyExistsException
     */
    public function createBook(Book $book): void
    {
        $existedBook = $this->bookRepository->findOneBy(['title' => $book->getTitle()]);
        if ($existedBook) {
            throw new AlreadyExistsException(sprintf('Book "%s" already exists.', $book->getTitle()));
        }

        $this->bookRepository->save($book, true);
    }

    /**
     * @param int $id
     * @param string|null $locale
     *
     * @return Book
     *
     * @throws NotFoundException
     */
    public function getBook(int $id, ?string $locale = Locale::DEFAULT_LOCALE): Book
    {
        $book = $this->bookRepository->findOneById($id);

        if (!$book) {
            throw new NotFoundException();
        }
        if ($locale === Locale::DEFAULT_LOCALE) {
            return $book;
        }

        // Load locale
        $this->translationService->changeLocale($book, $locale);
        foreach ($book->getAuthors() as $author) {
            $this->translationService->changeLocale($author, $locale);
        }

        return $book;
    }

    /**'
     * @param BookSearchParams $bookSearchParams
     * @param string|null $locale
     *
     * @return Book[]
     */
    public function searchBook(BookSearchParams $bookSearchParams, ?string $locale = Locale::DEFAULT_LOCALE): array
    {
        $books = $this->bookRepository->search($bookSearchParams);
        if ($locale === Locale::DEFAULT_LOCALE) {
            return $books;
        }

        // Load locale
        foreach ($books as $book) {
            $this->translationService->changeLocale($book, $locale);
            foreach ($book->getAuthors() as $author) {
                $this->translationService->changeLocale($author, $locale);
            }
        }

        return $books;
    }
}
