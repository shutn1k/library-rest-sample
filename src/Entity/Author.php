<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Author
 * @package App\Entity
 * @ORM\Entity(repositoryClass=AuthorRepository::class)
 * @ORM\Table(name="lr_author")
 */
class Author {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Book::class, mappedBy="authors")
     */
    private $books;

    /**
     * Author constructor.
     */
    public function __construct() {

        $this->books = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int {

        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {

        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self {

        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection {

        return $this->books;
    }

    /**
     * @param Book $book
     *
     * @return $this
     */
    public function addBook(Book $book): self {

        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->addAuthor($this);
        }

        return $this;
    }

    /**
     * @param Book $book
     *
     * @return $this
     */
    public function removeBook(Book $book): self {

        if ($this->books->removeElement($book)) {
            $book->removeAuthor($this);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string {

        return $this->getName();
    }
}
