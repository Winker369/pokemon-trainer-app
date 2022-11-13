<?php

namespace App\Enums;

use ArchTech\Enums\{Options, Values};

enum Reaction: string
{
    use Options, Values;

    case LIKE = 'Like';
    case HATE = 'Hate';
}
