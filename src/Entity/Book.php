<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * Class Book
 * @package App\Entity
 * @ORM\Entity(repositoryClass=BookRepository::class)
 * @ORM\Table(name="lr_book")
 */
class Book implements TranslatableInterface {

    use TranslatableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @MaxDepth(1)
     * @ORM\ManyToMany(targetEntity=Author::class, inversedBy="books")
     * @ORM\JoinTable(name="lr_book_author",
     *      joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="id")}
     * )
     */
    protected $authors;

    /**
     * @return int|null
     */
    public function getId(): ?int {

        return $this->id;
    }

    /**
     * Book constructor.
     */
    public function __construct() {

        $this->authors = new ArrayCollection();
    }

    /**
     * @return Collection|Author[]
     */
    public function getAuthors(): Collection {

        return $this->authors;
    }

    /**
     * @param Author $author
     *
     * @return $this
     */
    public function addAuthor(Author $author): self {

        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
        }

        return $this;
    }

    /**
     * @param Author $author
     *
     * @return $this
     */
    public function removeAuthor(Author $author): self {

        $this->authors->removeElement($author);

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string {

        return $this->translate($this->getCurrentLocale())->getName();
    }
}
