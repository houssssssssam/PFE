<?php

namespace App\Enums;

enum ConversationStatus: string
{
    case Open = 'open';
    case Ai = 'ai';
    case Expert = 'expert';
    case Closed = 'closed';
}
