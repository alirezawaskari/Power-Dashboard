<?php declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\RabbitMQService;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessNotifications extends Command
{
    protected $signature = 'notifications:process';
    protected $description = 'Process notifications from RabbitMQ queue';

    public function handle(RabbitMQService $rabbitMQ, NotificationService $notificationService): int
    {
        $this->info('Starting notification processor...');

        $rabbitMQ->consumeNotifications(function ($message) use ($notificationService) {
            try {
                $data = json_decode($message->body, true);
                
                $this->info("Processing notification: {$data['notification_id']}");
                
                // Here you would implement the actual notification delivery logic
                // For now, we'll just mark it as delivered
                $notificationService->markNotificationAsDelivered($data['notification_id']);
                
                $message->ack();
                
                $this->info("Notification {$data['notification_id']} processed successfully");
                
            } catch (\Exception $e) {
                Log::error('Failed to process notification', [
                    'error' => $e->getMessage(),
                    'data' => $data ?? null,
                ]);
                
                $message->reject();
                $this->error("Failed to process notification: {$e->getMessage()}");
            }
        });

        return 0;
    }
}
