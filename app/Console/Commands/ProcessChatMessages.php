<?php declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\RabbitMQService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessChatMessages extends Command
{
    protected $signature = 'chat:process';
    protected $description = 'Process chat messages from RabbitMQ queue';

    public function handle(RabbitMQService $rabbitMQ): int
    {
        $this->info('Starting chat message processor...');

        $rabbitMQ->consumeChatMessages(function ($message) {
            try {
                $data = json_decode($message->body, true);
                
                $this->info("Processing chat message for ticket: {$data['ticket_id']}");
                
                // Here you would implement the actual chat message delivery logic
                // This could involve WebSocket broadcasting, email notifications, etc.
                
                // For now, we'll just log the message
                Log::info('Chat message received', [
                    'ticket_id' => $data['ticket_id'],
                    'sender' => $data['sender_name'],
                    'message' => $data['message'],
                ]);
                
                $message->ack();
                
                $this->info("Chat message for ticket {$data['ticket_id']} processed successfully");
                
            } catch (\Exception $e) {
                Log::error('Failed to process chat message', [
                    'error' => $e->getMessage(),
                    'data' => $data ?? null,
                ]);
                
                $message->reject();
                $this->error("Failed to process chat message: {$e->getMessage()}");
            }
        });

        return 0;
    }
}
