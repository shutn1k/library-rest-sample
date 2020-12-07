<?php

namespace App\Manager;

use App\Entity\Book;
use App\Exceptions\AuthorNotFoundException;
use App\Exceptions\BookNotFoundException;
use App\Exceptions\ParserException;
use App\Exceptions\TranslationNotFoundException;
use App\Exceptions\JsonDecodeException;
use App\Model\BookFilter;
use App\Parser\BookParser;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class BookManager
 * @package App\Manager
 */
class BookManager {

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var AuthorManager */
    private $authorManager;

    /** @var string */
    private $defaultLocale;

    /**
     * BookManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param AuthorManager $authorManager
     * @param string $defaultLocale
     */
    public function __construct(EntityManagerInterface $entityManager, AuthorManager $authorManager, string $defaultLocale) {

        $this->entityManager = $entityManager;
        $this->authorManager = $authorManager;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param string $json
     *
     * @return Book
     * @throws AuthorNotFoundException
     * @throws ParserException
     * @throws JsonDecodeException
     */
    public function create(string $json): Book {

        $bookArray = (new BookParser($json))->parse();
        $book = new Book();
        $book->translate('en')->setName($bookArray['name']['en']);
        $book->translate('ru')->setName($bookArray['name']['ru']);

        foreach ($bookArray['authors'] as $authorId) {

            $author = $this->authorManager->show($authorId);
            if (!$author) {
                throw new AuthorNotFoundException();
            }
            $book->addAuthor($author);
        }

        $this->entityManager->persist($book);
        $book->mergeNewTranslations();
        $this->entityManager->flush();

        return $book;
    }

    /**
     * @param BookFilter $bookFilter
     *
     * @return array
     */
    public function index(BookFilter $bookFilter): array {

        return $this->entityManager->getRepository(Book::class)
            ->findByFilter($bookFilter);
    }

    /**
     * @param int $id
     * @param string|null $locale
     *
     * @return Book
     * @throws BookNotFoundException
     * @throws TranslationNotFoundException
     */
    public function show(int $id, string $locale = null): Book {

        if (!$locale) {
            $locale = $this->defaultLocale;
        }

        $book = $this->entityManager->getRepository(Book::class)
            ->findOneBy(['id' => $id]);
        if (!$book) {
            throw new BookNotFoundException();
        }

        /** @var Book $book */
        if (!$book->translate($locale, false)->getName()) {
            throw new TranslationNotFoundException();
        }

        return $book;
    }
}