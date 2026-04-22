<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Expert = 'expert';
    case User = 'user';
}
