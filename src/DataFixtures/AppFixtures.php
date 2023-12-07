<?php

namespace App\DataFixtures;

use App\Entity\Library\Author;
use App\Entity\Library\Book;
use App\Repository\Library\AuthorRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Faker\Factory;
use Faker\Generator;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\Entity\Translation;

class AppFixtures extends Fixture
{
    private const AUTHOR_COUNT = 10000;
    private const BOOK_COUNT = 10000;
    private const MAX_AUTHORS_PER_BOOK = 4;

    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }


    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $translationRepository = $manager->getRepository(Translation::class);
        $authorRepository = $manager->getRepository(Author::class);
        for ($i = 0; $i < self::AUTHOR_COUNT; $i++) {
            $manager->persist($this->createAuthor($translationRepository));
            if ($i % 200 === 0) {
                $manager->flush();
                $manager->clear();
            }
        }
        $manager->flush();
        $manager->clear();

        for ($i = 0; $i < self::BOOK_COUNT; $i++) {
            $manager->persist($this->createBook($translationRepository, $authorRepository));
            if ($i % 200 === 0) {
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
    }

    /**
     * @param TranslationRepository|ObjectRepository $translationRepository
     *
     * @return Author
     */
    public function createAuthor(TranslationRepository|ObjectRepository $translationRepository): Author
    {
        $name = $this->faker->firstName() . ' ' . $this->faker->lastName();
        $author = new Author();
        $translationRepository->translate($author, 'name', 'en', $name);
        $translationRepository->translate($author, 'name', 'ru', $name . ' (ru)');

        return $author;
    }

    /**
     * @param TranslationRepository|ObjectRepository $translationRepository
     * @param AuthorRepository|ObjectRepository $authorRepository
     *
     * @return Book
     */
    public function createBook(
        TranslationRepository|ObjectRepository $translationRepository,
        AuthorRepository|ObjectRepository $authorRepository
    ): Book {
        $title = ucfirst($this->faker->words(mt_rand(1, 5), true));
        $book = new Book();
        $translationRepository->translate($book, 'title', 'en', $title);
        $translationRepository->translate($book, 'title', 'ru', $title . ' (ru)');
        for ($i = 0; $i < self::MAX_AUTHORS_PER_BOOK; $i++) {
            $book->addAuthor($authorRepository->getRandomAuthor());
        }

        return $book;
    }
}
