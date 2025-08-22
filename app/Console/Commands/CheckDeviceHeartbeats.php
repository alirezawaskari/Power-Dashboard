<?php declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Device;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckDeviceHeartbeats extends Command
{
    protected $signature = 'devices:check-heartbeats';
    protected $description = 'Check device heartbeats and mark offline devices';

    public function handle(NotificationService $notificationService): int
    {
        $this->info('Checking device heartbeats...');

        $offlineCount = 0;
        $devices = Device::where('status', 'online')->get();

        foreach ($devices as $device) {
            if ($device->shouldBeOffline()) {
                $this->info("Device {$device->name} should be marked offline");
                
                $device->markOffline();
                $notificationService->notifyDeviceOffline($device);
                
                $offlineCount++;
            }
        }

        $this->info("Marked {$offlineCount} devices as offline");
        return 0;
    }
}
