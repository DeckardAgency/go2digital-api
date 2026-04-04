<?php

declare(strict_types=1);

namespace App\Enum;

enum ContentStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
}
