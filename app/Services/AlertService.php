<?php declare(strict_types=1);

namespace App\Services;

use App\Models\{Alert, Device, PowerRecord, User};
use App\Enums\{AlertType, AlertStatus, NotificationType};
use App\Models\EventLog;
use App\Enums\EventType;
use Illuminate\Support\Facades\Log;

class AlertService
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function checkPowerThreshold(Device $device, float $currentPower, float $threshold): ?Alert
    {
        if ($currentPower <= $threshold) {
            return null;
        }

        // Check if there's already an active alert for this device and threshold
        $existingAlert = Alert::where('device_id', $device->id)
            ->where('type', AlertType::PowerThreshold->value)
            ->where('threshold_value', $threshold)
            ->where('status', AlertStatus::Active->value)
            ->first();

        if ($existingAlert) {
            // Update existing alert with new current value
            $existingAlert->update([
                'current_value' => $currentPower,
                'triggered_at' => now(),
            ]);
            return $existingAlert;
        }

        // Create new alert
        $alert = Alert::create([
            'user_id' => $device->user_id,
            'device_id' => $device->id,
            'type' => AlertType::PowerThreshold->value,
            'title' => 'Power Threshold Exceeded',
            'message' => "Device '{$device->name}' power consumption ({$currentPower}W) exceeded threshold ({$threshold}W)",
            'threshold_value' => $threshold,
            'current_value' => $currentPower,
            'status' => AlertStatus::Active->value,
            'triggered_at' => now(),
            'escalation_level' => 0,
            'notification_sent' => false,
        ]);

        // Log the alert
        EventLog::create([
            'type' => EventType::AlertTriggered->value,
            'actor_type' => 'device',
            'actor_id' => $device->id,
            'subject_type' => 'alert',
            'subject_id' => $alert->id,
            'message' => "Power threshold alert triggered for device {$device->name}",
            'context' => [
                'device_id' => $device->id,
                'alert_id' => $alert->id,
                'threshold' => $threshold,
                'current_value' => $currentPower,
            ],
            'occurred_at' => now(),
        ]);

        // Send notification
        $this->notificationService->notifyPowerThreshold($device, $currentPower, $threshold);
        $alert->markNotificationSent();

        return $alert;
    }

    public function checkDeviceOffline(Device $device): ?Alert
    {
        // Check if there's already an active offline alert for this device
        $existingAlert = Alert::where('device_id', $device->id)
            ->where('type', AlertType::DeviceOffline->value)
            ->where('status', AlertStatus::Active->value)
            ->first();

        if ($existingAlert) {
            return $existingAlert;
        }

        // Create new offline alert
        $alert = Alert::create([
            'user_id' => $device->user_id,
            'device_id' => $device->id,
            'type' => AlertType::DeviceOffline->value,
            'title' => 'Device Offline',
            'message' => "Device '{$device->name}' has gone offline",
            'status' => AlertStatus::Active->value,
            'triggered_at' => now(),
            'escalation_level' => 0,
            'notification_sent' => false,
        ]);

        // Log the alert
        EventLog::create([
            'type' => EventType::AlertTriggered->value,
            'actor_type' => 'device',
            'actor_id' => $device->id,
            'subject_type' => 'alert',
            'subject_id' => $alert->id,
            'message' => "Device offline alert triggered for {$device->name}",
            'context' => [
                'device_id' => $device->id,
                'alert_id' => $alert->id,
            ],
            'occurred_at' => now(),
        ]);

        // Send notification
        $this->notificationService->notifyDeviceOffline($device);
        $alert->markNotificationSent();

        return $alert;
    }

    public function resolveDeviceOfflineAlert(Device $device): void
    {
        $alert = Alert::where('device_id', $device->id)
            ->where('type', AlertType::DeviceOffline->value)
            ->where('status', AlertStatus::Active->value)
            ->first();

        if ($alert) {
            $alert->resolve();

            EventLog::create([
                'type' => EventType::AlertResolved->value,
                'actor_type' => 'device',
                'actor_id' => $device->id,
                'subject_type' => 'alert',
                'subject_id' => $alert->id,
                'message' => "Device offline alert resolved for {$device->name}",
                'context' => [
                    'device_id' => $device->id,
                    'alert_id' => $alert->id,
                ],
                'occurred_at' => now(),
            ]);
        }
    }

    public function acknowledgeAlert(Alert $alert, User $user): void
    {
        $alert->acknowledge();

        EventLog::create([
            'type' => EventType::AlertAcknowledged->value,
            'actor_type' => 'user',
            'actor_id' => $user->id,
            'subject_type' => 'alert',
            'subject_id' => $alert->id,
            'message' => "Alert acknowledged by {$user->name}",
            'context' => [
                'alert_id' => $alert->id,
                'user_id' => $user->id,
            ],
            'occurred_at' => now(),
        ]);
    }

    public function resolveAlert(Alert $alert, User $user): void
    {
        $alert->resolve();

        EventLog::create([
            'type' => EventType::AlertResolved->value,
            'actor_type' => 'user',
            'actor_id' => $user->id,
            'subject_type' => 'alert',
            'subject_id' => $alert->id,
            'message' => "Alert resolved by {$user->name}",
            'context' => [
                'alert_id' => $alert->id,
                'user_id' => $user->id,
            ],
            'occurred_at' => now(),
        ]);
    }

    public function escalateAlerts(): void
    {
        $alerts = Alert::active()->get();

        foreach ($alerts as $alert) {
            if ($alert->needsEscalation()) {
                $alert->escalate();

                // Send escalation notification
                $this->notificationService->createNotification(
                    user: $alert->user,
                    type: NotificationType::AlertEscalated,
                    title: 'Alert Escalation',
                    message: "Alert '{$alert->title}' has been escalated to level {$alert->escalation_level}",
                    data: [
                        'alert_id' => $alert->id,
                        'escalation_level' => $alert->escalation_level,
                    ],
                    channel: 'email',
                    priority: 3
                );

                EventLog::create([
                    'type' => EventType::AlertEscalated->value,
                    'actor_type' => 'system',
                    'actor_id' => null,
                    'subject_type' => 'alert',
                    'subject_id' => $alert->id,
                    'message' => "Alert escalated to level {$alert->escalation_level}",
                    'context' => [
                        'alert_id' => $alert->id,
                        'escalation_level' => $alert->escalation_level,
                    ],
                    'occurred_at' => now(),
                ]);
            }
        }
    }
}
