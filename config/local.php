<?php

return [
    'settings' => [
        'displayErrorDetails' => true,
        'log_output' => [
            'name'  => 'slim-app',
            'level' => Monolog\Logger::DEBUG,
            'path'  => __DIR__ . '/../logs/app.log',
        ],
    ],
];
