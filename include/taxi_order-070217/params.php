<?php

$sl = DIRECTORY_SEPARATOR;

return [
    [
        "id" => "1",
        "active" => true,
        "path" => __DIR__ . $sl . "adapter_order",
        "configPath" => __DIR__ . $sl . "adapter_order" . $sl . "config" . $sl . "main.php",
        "userParams" => [
            "map" => false,
            "adapter" => [
                "id" => "taxiMaster"
            ],
        ],
    ],
    [
        "id" => "2",
        "active" => false,
        "path" => __DIR__ . $sl . "bitrix_order",
        "configPath" => __DIR__ . $sl . "bitrix_order" . $sl . "config" . $sl . "main.php",
        "userParams" => [
            "map" => false,
            "adapter" => false,
        ],
    ]
];
