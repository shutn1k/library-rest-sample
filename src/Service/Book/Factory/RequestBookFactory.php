<?php

namespace App\Service\Book\Factory;

use App\Entity\Library\Author;
use App\Entity\Library\Book;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Form\FormErrorArrayExtractor;
use App\Form\Library\BookType;
use App\Service\Author\AuthorService;
use App\Service\Book\BookService;
use RuntimeException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestBookFactory implements BookFactoryInterface
{
    /**
     * @param FormFactoryInterface $formFactory
     * @param FormErrorArrayExtractor $errorArrayExtractor
     * @param AuthorService $authorService
     */
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly FormErrorArrayExtractor $errorArrayExtractor,
        private readonly AuthorService $authorService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(mixed $request): Book
    {
        if (!$request instanceof Request) {
            throw new RuntimeException(
                sprintf('Param $data must be of type %s, %s given.', Request::class, get_class($request))
            );
        }

        $book = new Book();
        $bookForm = $this->formFactory->create(BookType::class, $book);
        $bookForm->handleRequest($request);
        if ($bookForm->isSubmitted() && $bookForm->isValid()) {
            $bookIds = $bookForm->get('author_ids')->getData();
            foreach ($bookIds as $bookId) {
                try {
                    $book->addAuthor($this->authorService->getAuthor($bookId));
                } catch (NotFoundException) {
                    throw new BadRequestException(errors: ['author_ids' => ["Author with id $bookId not found."]]);
                }
            }
            $book->setTranslatableLocale($request->getLocale());

            return $book;
        }

        throw new BadRequestException(errors: $this->errorArrayExtractor->extract($bookForm));
    }
}
