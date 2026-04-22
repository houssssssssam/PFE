<?php

namespace App\Enums;

enum MessageSenderType: string
{
    case User = 'user';
    case Ai = 'ai';
    case Expert = 'expert';
}
