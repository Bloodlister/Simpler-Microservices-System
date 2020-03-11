<?php
return [
    'database'  => [
        'host'     => getenv('DB_HOST'),
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASS'),
        'database' => getenv('DB_DATABASE'),
    ],
    'rabbit_mq' => [
        'host'     => getenv('RABBITMQ_HOST'),
        'port'     => getenv('RABBITMQ_PORT'),
        'username' => getenv('RABBITMQ_USER'),
        'password' => getenv('RABBITMQ_PASS'),
    ],
];
