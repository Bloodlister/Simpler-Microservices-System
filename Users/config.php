<?php
return [
    'database'  => [
        'host'     => getenv('POSTGRES_HOST'),
        'username' => getenv('POSTGRES_USER'),
        'password' => getenv('POSTGRES_PASS'),
        'database' => getenv('POSTGRES_DB'),
    ],
    'rabbit_mq' => [
        'host'     => getenv('RABBITMQ_HOST'),
        'port'     => getenv('RABBITMQ_PORT'),
        'username' => getenv('RABBITMQ_USER'),
        'password' => getenv('RABBITMQ_PASS'),
    ],
];
