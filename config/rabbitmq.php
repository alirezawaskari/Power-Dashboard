<?php

return [
    'host' => env('RABBITMQ_HOST', 'localhost'),
    'port' => env('RABBITMQ_PORT', 5672),
    'user' => env('RABBITMQ_USER', 'guest'),
    'password' => env('RABBITMQ_PASSWORD', 'guest'),
    'vhost' => env('RABBITMQ_VHOST', '/'),
    
    'exchanges' => [
        'notifications' => [
            'name' => 'notifications',
            'type' => 'topic',
            'durable' => true,
        ],
        'chat' => [
            'name' => 'chat',
            'type' => 'topic',
            'durable' => true,
        ],
    ],
    
    'queues' => [
        'notifications' => [
            'name' => 'notifications_queue',
            'durable' => true,
            'bindings' => [
                'notifications' => 'notification.*',
            ],
        ],
        'chat' => [
            'name' => 'chat_queue',
            'durable' => true,
            'bindings' => [
                'chat' => 'chat.*',
            ],
        ],
    ],
];
