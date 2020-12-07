<?php

namespace App\Manager;

use App\Entity\Author;
use App\Exceptions\AuthorNotFoundException;
use App\Exceptions\JsonDecodeException;
use App\Exceptions\ParserException;
use App\Parser\AuthorParser;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AuthorManager
 * @package App\Manager
 */
class AuthorManager {

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * AuthorManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) {

        $this->entityManager = $entityManager;
    }

    /**
     * @param string $json
     *
     * @return Author|null
     * @throws JsonDecodeException
     * @throws ParserException
     */
    public function create(string $json): ?Author {

        $authorArray = (new AuthorParser($json))->parse();
        $author = (new Author())
            ->setName($authorArray['name']);

        $this->entityManager->persist($author);
        $this->entityManager->flush();

        return $author;
    }

    /**
     * @param int $id
     *
     * @return Author
     * @throws AuthorNotFoundException
     */
    public function show(int $id): Author {

        $author = $this->entityManager->getRepository(Author::class)
            ->findOneBy(['id' => $id]);
        if (!$author) {
            throw new AuthorNotFoundException();
        }

        return $author;
    }
}