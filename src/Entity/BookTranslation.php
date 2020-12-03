<?php

namespace App\Entity;

use App\Repository\BookTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * Class BookTranslation
 * @package App\Entity
 * @ORM\Entity(repositoryClass=BookTranslationRepository::class)
 * @ORM\Table(name="lr_book_translation")
 */
class BookTranslation implements TranslationInterface {

    use TranslationTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

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
     * @return string
     */
    public function __toString(): string {

        return $this->getName();
    }
}
