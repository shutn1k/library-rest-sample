<?php

namespace App\Constants;

use OpenApi\Attributes as OA;

#[OA\Schema(
    enum: [Locale::en, Locale::ru]
)]
enum Locale
{
    public const DEFAULT_LOCALE = 'en';

    case en;
    case ru;
}
