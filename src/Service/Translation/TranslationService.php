<?php

namespace App\Service\Translation;

use App\Constants\Locale;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;

class TranslationService
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function changeLocale(mixed $entity, string $locale = Locale::DEFAULT_LOCALE): void
    {
        if (!$entity instanceof TranslatableInterface) {
            throw new RuntimeException(sprintf('Class must implements %s', TranslatableInterface::class));
        }

        $entity->setTranslatableLocale($locale);
        $this->entityManager->refresh($entity);
    }
}
