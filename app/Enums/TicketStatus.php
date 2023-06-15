<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN = 'OPEN';
    case RESOLVED = 'RESOLVED';
    case REJECTED = 'REJECTED';
}