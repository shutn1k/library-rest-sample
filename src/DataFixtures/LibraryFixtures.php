<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class LibraryFixtures
 * @package App\DataFixtures
 */
final class LibraryFixtures extends Fixture {

    private const MAX_COUNT = 199;

    private const MAX_BOOK_AUTHORS = 3;

    /** Шанс выпадания 1 автора MAX_RAND - MAX_BOOK_AUTHORS + 1  */
    private const MAX_RAND = 10;

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void {

        $faker = Factory::create();

        $authors = [];
        $books = [];
        for ($i = 0; $i <= self::MAX_COUNT; $i++) {

            $author = (new Author())
                ->setName($faker->name);
            $manager->persist($author);
            $authors[] = $author;

            $name = $faker->sentence(rand(1, 5));
            $book = new Book();
            $book->translate('en')->setName($name);
            $book->translate('ru')->setName($name . ' (русский)');
            // У книги как минимум 1 автор и наоборот
            $book->addAuthor($author);
            $books[] = $book;
        }

        // Если повезет добавим 1-2 соавтора
        foreach ($books as $book) {

            $rand = rand(1, self::MAX_RAND);
            $authorCount = $rand <= self::MAX_BOOK_AUTHORS ? $rand : 1;
            if ($authorCount > 1) {
                for ($i = 1; $i < $authorCount; $i++) {
                    $randomAuthor = $authors[rand(0, self::MAX_COUNT)];
                    /** @var Book $book */
                    if (!$book->getAuthors()->contains($randomAuthor)) {
                        $book->addAuthor($randomAuthor);
                    }
                }
            }

            $manager->persist($book);
            $book->mergeNewTranslations();
        }

        $manager->flush();
    }
}
