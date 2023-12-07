<?php

namespace App\Entity\Library;

use App\Repository\Library\AuthorRepository;
use App\Service\Translation\TranslatableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use OpenApi\Attributes as OA;
use Stringable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[OA\Schema(
    description: 'Author entity',
    required: ['id', 'name']
)]
#[OA\RequestBody(
    request: 'AuthorCreate',
    content: [new OA\JsonContent(required: ['name'], properties: [
        new OA\Property('name', type: 'string', maxLength: 255, minLength: 3),
        new OA\Property('books_ids', type: 'array', items: new OA\Items(type: 'integer', minimum: 1)),
    ])]
)]
#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[ORM\Table(name: 'l_author')]
class Author implements Stringable, TranslatableInterface
{
    #[OA\Property(nullable: false)]
    #[Groups(['api_v1'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[OA\Property(maxLength: 255, minLength: 3, nullable: false)]
    #[Groups(['api_v1'])]
    #[ORM\Column(length: 255)]
    #[Gedmo\Translatable]
    private string $name;
    #[OA\Property(type: 'array', items: new OA\Items(ref: '#/components/schemas/Book'), nullable: false)]
    #[Groups(['api_v1_author'])]
    #[MaxDepth(1)]
    #[ORM\ManyToMany(targetEntity: Book::class, mappedBy: 'authors')]
    private Collection $books;
    #[Gedmo\Locale]
    private $locale;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Author
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * @param Book $book
     *
     * @return Author
     */
    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->addAuthor($this);
        }

        return $this;
    }

    /**
     * @param Book $book
     *
     * @return Author
     */
    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            $book->removeAuthor($this);
        }

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return void
     */
    public function setTranslatableLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getName();
    }
}
