<?php declare(strict_types=1);

namespace App\Enums;

enum AlertStatus: string
{
    case Active = 'active';
    case Acknowledged = 'acknowledged';
    case Resolved = 'resolved';
    case Suppressed = 'suppressed';
}
