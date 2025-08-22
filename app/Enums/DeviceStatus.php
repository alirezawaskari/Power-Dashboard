<?php declare(strict_types=1);
namespace App\Enums;
enum DeviceStatus: string
{
    case Online = 'online';
    case Offline = 'offline';
    case Maintenance = 'maintenance';
    case Decommissioned = 'decommissioned';
}
