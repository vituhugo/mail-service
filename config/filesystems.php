<?php

return [
    'default' => 'local',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => getcwd(),
        ],
        'database' => [
            'driver' => 'database',
            'root' => 'ROT_ARQUIVO',
        ]
    ],
];
