<?php

namespace App\Entity\Library;

use App\Repository\Library\BookRepository;
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
    description: 'Book entity',
    required: ['id', 'title']
)]
#[OA\RequestBody(
    request: 'BookCreate',
    content: [new OA\JsonContent(required: ['name'], properties: [
        new OA\Property('name', type: 'string', maxLength: 255, minLength: 3),
        new OA\Property('author_ids', type: 'array', items: new OA\Items(type: 'integer', minimum: 1)),
    ])]
)]
#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: 'l_book')]
#[ORM\Index(columns: ['title'], name: 'title_idx')]
class Book implements Stringable, TranslatableInterface
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
    private string $title;
    #[OA\Property(type: 'array', items: new OA\Items(ref: '#/components/schemas/Author'), nullable: false)]
    #[Groups(['api_v1_book'])]
    #[MaxDepth(1)]
    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books')]
    #[ORM\JoinTable(name: 'l_book_author')]
    private Collection $authors;
    #[Gedmo\Locale]
    private $locale;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Book
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    /**
     * @param Author $author
     *
     * @return Book
     */
    public function addAuthor(Author $author): static
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    /**
     * @param Author $author
     *
     * @return Book
     */
    public function removeAuthor(Author $author): static
    {
        $this->authors->removeElement($author);

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
        return (string)$this->getTitle();
    }
}
