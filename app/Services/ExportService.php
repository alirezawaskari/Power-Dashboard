<?php declare(strict_types=1);

namespace App\Services;

use App\Models\{Device, PowerRecord, Ticket, Alert, User, EventLog};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ExportService
{
    public function exportPowerData(Device $device, Carbon $from, Carbon $to, string $format = 'csv'): string
    {
        $records = PowerRecord::where('device_id', $device->id)
            ->whereBetween('ts', [$from, $to])
            ->orderBy('ts')
            ->get();

        if ($format === 'json') {
            return $this->exportToJson($records, 'power_data');
        }

        return $this->exportToCsv($records, 'power_data', [
            'timestamp' => 'ts',
            'current' => 'current',
            'voltage' => 'voltage',
            'power' => 'power',
            'phase' => 'phase',
            'sampling_ms' => 'sampling_ms',
        ]);
    }

    public function exportDeviceData(User $user, string $format = 'csv'): string
    {
        $devices = Device::where('user_id', $user->id)->get();

        if ($format === 'json') {
            return $this->exportToJson($devices, 'devices');
        }

        return $this->exportToCsv($devices, 'devices', [
            'id' => 'id',
            'uuid' => 'uuid',
            'name' => 'name',
            'slug' => 'slug',
            'status' => 'status',
            'firmware' => 'firmware',
            'model' => 'model',
            'location' => 'location',
            'last_seen' => 'last_seen_at',
            'created' => 'created_at',
        ]);
    }

    public function exportTicketData(User $user, string $format = 'csv'): string
    {
        $tickets = Ticket::where('user_id', $user->id)
            ->orWhere('assignee_id', $user->id)
            ->with(['creator', 'assignee', 'device'])
            ->get();

        if ($format === 'json') {
            return $this->exportToJson($tickets, 'tickets');
        }

        return $this->exportToCsv($tickets, 'tickets', [
            'id' => 'id',
            'title' => 'title',
            'status' => 'status',
            'priority' => 'priority',
            'creator' => 'creator.name',
            'assignee' => 'assignee.name',
            'device' => 'device.name',
            'created' => 'created_at',
            'updated' => 'updated_at',
        ]);
    }

    public function exportAlertData(User $user, string $format = 'csv'): string
    {
        $alerts = Alert::where('user_id', $user->id)
            ->with(['device'])
            ->orderBy('triggered_at', 'desc')
            ->get();

        if ($format === 'json') {
            return $this->exportToJson($alerts, 'alerts');
        }

        return $this->exportToCsv($alerts, 'alerts', [
            'id' => 'id',
            'type' => 'type',
            'title' => 'title',
            'message' => 'message',
            'status' => 'status',
            'device' => 'device.name',
            'threshold' => 'threshold_value',
            'current' => 'current_value',
            'triggered' => 'triggered_at',
            'acknowledged' => 'acknowledged_at',
            'resolved' => 'resolved_at',
            'escalation' => 'escalation_level',
        ]);
    }

    public function exportUserActivity(User $user, Carbon $from, Carbon $to, string $format = 'csv'): string
    {
        $events = EventLog::where('actor_type', 'user')
            ->where('actor_id', $user->id)
            ->whereBetween('occurred_at', [$from, $to])
            ->orderBy('occurred_at')
            ->get();

        if ($format === 'json') {
            return $this->exportToJson($events, 'user_activity');
        }

        return $this->exportToCsv($events, 'user_activity', [
            'timestamp' => 'occurred_at',
            'type' => 'type',
            'message' => 'message',
            'subject_type' => 'subject_type',
            'subject_id' => 'subject_id',
        ]);
    }

    private function exportToCsv($data, string $type, array $fields): string
    {
        $filename = "export_{$type}_" . now()->format('Y-m-d_H-i-s') . '.csv';
        $path = "exports/{$filename}";

        $handle = fopen('php://temp', 'r+');

        // Write headers
        fputcsv($handle, array_keys($fields));

        // Write data
        foreach ($data as $item) {
            $row = [];
            foreach ($fields as $header => $field) {
                $value = $this->getNestedValue($item, $field);
                $row[] = $value instanceof Carbon ? $value->toISOString() : $value;
            }
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        // Store file
        Storage::put($path, $csv);

        return $path;
    }

    private function exportToJson($data, string $type): string
    {
        $filename = "export_{$type}_" . now()->format('Y-m-d_H-i-s') . '.json';
        $path = "exports/{$filename}";

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        Storage::put($path, $json);

        return $path;
    }

    private function getNestedValue($object, string $field)
    {
        if (str_contains($field, '.')) {
            $parts = explode('.', $field);
            $value = $object;
            foreach ($parts as $part) {
                $value = $value->{$part} ?? null;
            }
            return $value;
        }

        return $object->{$field} ?? null;
    }

    public function getExportUrl(string $path): string
    {
        return Storage::url($path);
    }

    public function cleanupOldExports(int $days = 7): void
    {
        $files = Storage::files('exports');
        $cutoff = now()->subDays($days);

        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp(Storage::lastModified($file));
            if ($lastModified->lt($cutoff)) {
                Storage::delete($file);
            }
        }
    }
}
