<?php

namespace App\Tests\Service\Book;

use App\Entity\Library\Book;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\NotFoundException;
use App\Repository\Library\BookRepository;
use App\Service\Book\BookService;
use App\Service\Translation\TranslationService;
use PHPUnit\Framework\TestCase;

class BookServiceTest extends TestCase
{
    /**
     * @return void
     *
     * @throws AlreadyExistsException
     */
    public function testCreateBookNormal(): void
    {
        $bookRepositoryMocked = $this->createMock(BookRepository::class);
        $bookRepositoryMocked->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue(null));
        $bookRepositoryMocked->expects($this->once())
            ->method('save');

        $translationServiceMocked = $this->createMock(TranslationService::class);

        $bookService = new BookService($bookRepositoryMocked, $translationServiceMocked);
        $bookService->createBook((new Book())
            ->setTitle('Test Book'));
    }

    /**
     * @return void
     *
     * @throws AlreadyExistsException
     */
    public function testCreateAlreadyExists(): void
    {
        $bookRepositoryMocked = $this->createMock(BookRepository::class);
        $bookRepositoryMocked->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue(new Book()));

        $translationServiceMocked = $this->createMock(TranslationService::class);

        $bookService = new BookService($bookRepositoryMocked, $translationServiceMocked);
        $this->expectException(AlreadyExistsException::class);
        $bookService->createBook((new Book())
            ->setTitle('Test Book'));
    }

    /**
     * @return void
     *
     * @throws NotFoundException
     */
    public function testGetBookNormal(): void
    {
        $bookRepositoryMocked = $this->createMock(BookRepository::class);
        $bookRepositoryMocked->expects($this->once())
            ->method('__call')
            ->will($this->returnValue((new Book())->setTitle('Test Book')));

        $translationServiceMocked = $this->createMock(TranslationService::class);

        $bookService = new BookService($bookRepositoryMocked, $translationServiceMocked);
        $book = $bookService->getBook(2);

        $this->assertEquals($book->getTitle(), 'Test Book');
    }

    /**
     * @return void
     *
     * @throws NotFoundException
     */
    public function testGetBookNotFound(): void
    {
        $bookRepositoryMocked = $this->createMock(BookRepository::class);
        $bookRepositoryMocked->expects($this->once())
            ->method('__call')
            ->will($this->returnValue(null));

        $translationServiceMocked = $this->createMock(TranslationService::class);

        $bookService = new BookService($bookRepositoryMocked, $translationServiceMocked);
        $this->expectException(NotFoundException::class);
        $bookService->getBook(2);
    }
}
