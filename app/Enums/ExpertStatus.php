<?php

namespace App\Enums;

enum ExpertStatus: string
{
    case Pending = 'pending';
    case Validated = 'validated';
    case Rejected = 'rejected';
}
