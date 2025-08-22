<?php declare(strict_types=1);

namespace App\Services;

use App\Models\{Device, User, Alert, EventLog};
use App\Enums\{DeviceStatus, EventType};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulkOperationService
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly AlertService $alertService
    ) {}

    public function bulkUpdateDeviceStatus(Collection $devices, DeviceStatus $status, User $user): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        DB::beginTransaction();
        try {
            foreach ($devices as $device) {
                try {
                    $oldStatus = $device->status;
                    
                    switch ($status) {
                        case DeviceStatus::Online:
                            $device->markOnline();
                            break;
                        case DeviceStatus::Offline:
                            $device->markOffline();
                            break;
                        case DeviceStatus::Maintenance:
                            $device->setMaintenanceMode();
                            break;
                        case DeviceStatus::Decommissioned:
                            $device->decommission();
                            break;
                    }

                    // Log the status change
                    EventLog::create([
                        'type' => EventType::DeviceStatusChanged->value,
                        'actor_type' => 'user',
                        'actor_id' => $user->id,
                        'subject_type' => 'device',
                        'subject_id' => $device->id,
                        'message' => "Device status changed from {$oldStatus->value} to {$status->value}",
                        'context' => [
                            'device_id' => $device->id,
                            'old_status' => $oldStatus->value,
                            'new_status' => $status->value,
                            'bulk_operation' => true,
                        ],
                        'occurred_at' => now(),
                    ]);

                    $results['success']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'device_id' => $device->id,
                        'error' => $e->getMessage(),
                    ];
                    Log::error('Bulk operation failed for device', [
                        'device_id' => $device->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }

    public function bulkAssignDevicesToUser(Collection $devices, User $newOwner, User $operator): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        DB::beginTransaction();
        try {
            foreach ($devices as $device) {
                try {
                    $oldOwnerId = $device->user_id;
                    $device->update(['user_id' => $newOwner->id]);

                    // Log the ownership change
                    EventLog::create([
                        'type' => EventType::DeviceTransferred->value,
                        'actor_type' => 'user',
                        'actor_id' => $operator->id,
                        'subject_type' => 'device',
                        'subject_id' => $device->id,
                        'message' => "Device transferred from user {$oldOwnerId} to user {$newOwner->id}",
                        'context' => [
                            'device_id' => $device->id,
                            'old_owner_id' => $oldOwnerId,
                            'new_owner_id' => $newOwner->id,
                            'bulk_operation' => true,
                        ],
                        'occurred_at' => now(),
                    ]);

                    $results['success']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'device_id' => $device->id,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }

    public function bulkDeleteDevices(Collection $devices, User $user): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        DB::beginTransaction();
        try {
            foreach ($devices as $device) {
                try {
                    $deviceId = $device->id;
                    $deviceName = $device->name;
                    
                    // Delete related records first
                    $device->records()->delete();
                    $device->tickets()->delete();
                    Alert::where('device_id', $device->id)->delete();
                    
                    // Delete the device
                    $device->delete();

                    // Log the deletion
                    EventLog::create([
                        'type' => EventType::DeviceDeleted->value,
                        'actor_type' => 'user',
                        'actor_id' => $user->id,
                        'subject_type' => 'device',
                        'subject_id' => $deviceId,
                        'message' => "Device '{$deviceName}' deleted",
                        'context' => [
                            'device_id' => $deviceId,
                            'device_name' => $deviceName,
                            'bulk_operation' => true,
                        ],
                        'occurred_at' => now(),
                    ]);

                    $results['success']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'device_id' => $device->id,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }

    public function bulkAcknowledgeAlerts(Collection $alerts, User $user): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($alerts as $alert) {
            try {
                $this->alertService->acknowledgeAlert($alert, $user);
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'alert_id' => $alert->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    public function bulkResolveAlerts(Collection $alerts, User $user): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($alerts as $alert) {
            try {
                $this->alertService->resolveAlert($alert, $user);
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'alert_id' => $alert->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    public function bulkSendNotification(Collection $users, string $title, string $message, array $data = []): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($users as $user) {
            try {
                $this->notificationService->createNotification(
                    user: $user,
                    type: \App\Enums\NotificationType::SystemAlert,
                    title: $title,
                    message: $message,
                    data: $data,
                    channel: 'websocket',
                    priority: 2
                );
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    public function getBulkOperationProgress(string $operationId): array
    {
        // This would typically query a job queue or cache to get progress
        // For now, return a simple structure
        return [
            'operation_id' => $operationId,
            'status' => 'completed',
            'progress' => 100,
            'total_items' => 0,
            'processed_items' => 0,
            'success_count' => 0,
            'error_count' => 0,
        ];
    }
}
