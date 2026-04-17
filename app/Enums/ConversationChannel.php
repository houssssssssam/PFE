<?php

namespace App\Enums;

enum ConversationChannel: string
{
    case Ai = 'ai';
    case Expert = 'expert';
    case Hybrid = 'hybrid';
}
