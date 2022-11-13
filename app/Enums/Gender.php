<?php

namespace App\Enums;

use ArchTech\Enums\{Options, Values};

enum Gender: string
{
    use Options, Values;

    case MALE = 'Male';
    case FEMALE = 'Female';
}
