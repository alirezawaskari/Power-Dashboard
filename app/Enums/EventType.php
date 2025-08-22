<?php declare(strict_types=1);

namespace App\Enums;

enum EventType: string
{
    // Ingest events
    case IngestAccepted = 'ingest.accepted';
    case IngestRejectedAuth = 'ingest.rejected_auth';
    case IngestRejectedSchema = 'ingest.rejected_schema';
    case IngestRateLimited = 'ingest.rate_limited';

    // Device events
    case DeviceOnline = 'device.online';
    case DeviceOffline = 'device.offline';
    case DeviceMaintenanceOn = 'device.maintenance_on';
    case DeviceMaintenanceOff = 'device.maintenance_off';
    case DeviceDecommissioned = 'device.decommissioned';

    // Ticket events
    case TicketCreated = 'ticket.created';
    case TicketUpdated = 'ticket.updated';
    case TicketClosed = 'ticket.closed';
    case TicketAssigned = 'ticket.assigned';

    // Settings events
    case SettingChanged = 'setting.changed';

    // Security events
    case AuthFailedDevice = 'auth.failed_device';
    case ApiKeyUsed = 'apikey.used';
    case PermissionDenied = 'permission.denied';

    // Notification events
    case NotificationSent = 'notification.sent';
    case NotificationDelivered = 'notification.delivered';
    case NotificationFailed = 'notification.failed';

    // Alert events
    case AlertTriggered = 'alert.triggered';
    case AlertAcknowledged = 'alert.acknowledged';
    case AlertResolved = 'alert.resolved';
    case AlertEscalated = 'alert.escalated';

    // Device management events
    case DeviceStatusChanged = 'device.status_changed';
    case DeviceTransferred = 'device.transferred';
    case DeviceDeleted = 'device.deleted';

    // Future expansion (v1.1)
    // case RuleCreated = 'rule.created';
    // case RuleUpdated = 'rule.updated';
    // case RuleDisabled = 'rule.disabled';
}
