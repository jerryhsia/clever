<?php
/**
 * application configurations shared by all test types
 */
return [
    'runtimePath' => __DIR__ . '/../runtime',
    'components' => [
        /*'mail' => [
            'useFileTransport' => true,
        ],*/
        'urlManager' => [
            'showScriptName' => true,
        ],
    ],
];
