<?php declare(strict_types=1);

namespace App\Enums;

enum NotificationType: string
{
    case DeviceOffline = 'device.offline';
    case DeviceOnline = 'device.online';
    case PowerThreshold = 'power.threshold';
    case TicketAssigned = 'ticket.assigned';
    case TicketUpdated = 'ticket.updated';
    case TicketClosed = 'ticket.closed';
    case SystemAlert = 'system.alert';
    case Maintenance = 'maintenance';
    case Security = 'security';
    case AlertEscalated = 'alert.escalated';
}
