<?php

namespace App\Service\Translation;

use Gedmo\Translatable\Translatable;

interface TranslatableInterface extends Translatable
{
    /**
     * @param string $locale
     *
     * @return void
     */
    public function setTranslatableLocale(string $locale): void;
}
