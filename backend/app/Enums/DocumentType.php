<?php

namespace App\Enums;

enum DocumentType: string
{
    case Diploma = 'diploma';
    case IdCard = 'id_card';
    case Certificate = 'certificate';
    case Other = 'other';
}
