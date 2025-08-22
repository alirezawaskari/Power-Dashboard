<?php declare(strict_types=1);

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use Illuminate\Support\Facades\Log;

class RabbitMQService
{
    private AMQPStreamConnection $connection;
    private \PhpAmqpLib\Channel\AMQPChannel $channel;
    
    private const EXCHANGE_NOTIFICATIONS = 'notifications';
    private const EXCHANGE_CHAT = 'chat';
    private const QUEUE_NOTIFICATIONS = 'notifications_queue';
    private const QUEUE_CHAT = 'chat_queue';

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            config('rabbitmq.host', 'localhost'),
            config('rabbitmq.port', 5672),
            config('rabbitmq.user', 'guest'),
            config('rabbitmq.password', 'guest'),
            config('rabbitmq.vhost', '/')
        );
        
        $this->channel = $this->connection->channel();
        $this->setupExchangesAndQueues();
    }

    private function setupExchangesAndQueues(): void
    {
        // Setup notifications exchange and queue
        $this->channel->exchange_declare(
            self::EXCHANGE_NOTIFICATIONS, 
            AMQPExchangeType::TOPIC, 
            false, 
            true, 
            false
        );
        
        $this->channel->queue_declare(
            self::QUEUE_NOTIFICATIONS, 
            false, 
            true, 
            false, 
            false
        );
        
        $this->channel->queue_bind(
            self::QUEUE_NOTIFICATIONS, 
            self::EXCHANGE_NOTIFICATIONS, 
            'notification.*'
        );

        // Setup chat exchange and queue
        $this->channel->exchange_declare(
            self::EXCHANGE_CHAT, 
            AMQPExchangeType::TOPIC, 
            false, 
            true, 
            false
        );
        
        $this->channel->queue_declare(
            self::QUEUE_CHAT, 
            false, 
            true, 
            false, 
            false
        );
        
        $this->channel->queue_bind(
            self::QUEUE_CHAT, 
            self::EXCHANGE_CHAT, 
            'chat.*'
        );
    }

    public function publishNotification(array $data, string $routingKey = 'notification.default'): void
    {
        $message = new AMQPMessage(
            json_encode($data),
            [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'content_type' => 'application/json',
                'timestamp' => time(),
            ]
        );

        $this->channel->basic_publish(
            $message, 
            self::EXCHANGE_NOTIFICATIONS, 
            $routingKey
        );

        Log::info('Notification published', ['routing_key' => $routingKey, 'data' => $data]);
    }

    public function publishChatMessage(array $data, string $routingKey = 'chat.message'): void
    {
        $message = new AMQPMessage(
            json_encode($data),
            [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'content_type' => 'application/json',
                'timestamp' => time(),
            ]
        );

        $this->channel->basic_publish(
            $message, 
            self::EXCHANGE_CHAT, 
            $routingKey
        );

        Log::info('Chat message published', ['routing_key' => $routingKey, 'data' => $data]);
    }

    public function consumeNotifications(callable $callback): void
    {
        $this->channel->basic_consume(
            self::QUEUE_NOTIFICATIONS,
            '',
            false,
            false,
            false,
            false,
            $callback
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function consumeChatMessages(callable $callback): void
    {
        $this->channel->basic_consume(
            self::QUEUE_CHAT,
            '',
            false,
            false,
            false,
            false,
            $callback
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function __destruct()
    {
        if (isset($this->channel)) {
            $this->channel->close();
        }
        if (isset($this->connection)) {
            $this->connection->close();
        }
    }
}
