<?php

namespace App\Service\Author\Factory;

use App\Entity\Library\Author;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Form\FormErrorArrayExtractor;
use App\Form\Library\AuthorType;
use App\Service\Book\BookService;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use RuntimeException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestAuthorFactory implements AuthorFactoryInterface
{
    /**
     * @param FormFactoryInterface $formFactory
     * @param FormErrorArrayExtractor $errorArrayExtractor
     * @param BookService $bookService
     */
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly FormErrorArrayExtractor $errorArrayExtractor,
        private readonly BookService $bookService
    ) {
    }

    /**
     * @param Request $request
     *
     * @return Author
     *
     * @throws BadRequestException
     */
    public function create(mixed $request): Author
    {
        if (!$request instanceof Request) {
            throw new RuntimeException(
                sprintf('Param $data must be of type %s, %s given.', Request::class, get_class($request))
            );
        }

        $author = new Author();
        $authorForm = $this->formFactory->create(AuthorType::class, $author);
        $authorForm->handleRequest($request);
        if ($authorForm->isSubmitted() && $authorForm->isValid()) {
            $bookIds = $authorForm->get('book_ids')->getData();
            foreach ($bookIds as $bookId) {
                try {
                    $author->addBook($this->bookService->getBook($bookId));
                } catch (NotFoundException) {
                    throw new BadRequestException(errors: ['book_ids' => ["Book with id $bookId not found."]]);
                }
            }
            $author->setTranslatableLocale($request->getLocale());

            return $author;
        }

        throw new BadRequestException(errors: $this->errorArrayExtractor->extract($authorForm));
    }
}
