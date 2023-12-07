<?php

namespace App\Service\Author;

use App\Constants\Locale;
use App\Entity\Library\Author;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\NotFoundException;
use App\Repository\Library\AuthorRepository;
use App\Service\Translation\TranslationService;
use Doctrine\ORM\EntityManagerInterface;

class AuthorService
{
    /**
     * @param AuthorRepository $authorRepository
     * @param TranslationService $translationService
     */
    public function __construct(
        private readonly AuthorRepository $authorRepository,
        private readonly TranslationService $translationService
    ) {
    }

    /**
     * @param Author $author
     *
     * @return void
     *
     * @throws AlreadyExistsException
     */
    public function createAuthor(Author $author): void
    {
        $existedAuthor = $this->authorRepository->findOneBy(['name' => $author->getName()]);
        if ($existedAuthor) {
            throw new AlreadyExistsException(sprintf('Author "%s" already exists.', $author->getName()));
        }

        $this->authorRepository->save($author, true);
    }

    /**
     * @param int $id
     * @param string|null $locale
     *
     * @return Author
     *
     * @throws NotFoundException
     */
    public function getAuthor(int $id, ?string $locale = Locale::DEFAULT_LOCALE): Author
    {
        $author = $this->authorRepository->findOneById($id);
        if (!$author) {
            throw new NotFoundException();
        }
        if ($locale === Locale::DEFAULT_LOCALE) {
            return $author;
        }

        // Load locale
        $this->translationService->changeLocale($author, $locale);
        foreach ($author->getBooks() as $book) {
            $this->translationService->changeLocale($book, $locale);
        }

        return $author;
    }
}
