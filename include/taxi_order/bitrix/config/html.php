<?php

return [
    "form" => [
        "id" => "taxi-order",
        "inputs" => [
            "id" => [
                "tariffs" => "FIELD_TARIFFS",
                "time" => "FIELD_TIME",
                "from" => [
                    "autocomplete" => [
                        "street" => "FIELD_FROM_AUTOCOMPLETE_STREET",
                    ],
                    "address" => [
                        "street" => "FIELD_FROM_STREET",
                        "house" => "FIELD_FROM_HOUSE",
                        "porch" => "FIELD_FROM_PORCH",
                    ],
                    "comment" => "FIELD_FROM_COMMENT",
                    "wishes" => "FIELD_FROM_WISHES",
                ],
                "to" => [
                    "address" => [
                        "street" => "FIELD_TO_STREET",
                        "house" => "FIELD_TO_HOUSE",
                        "porch" => "FIELD_TO_PORCH",
                    ],
                    "autocomplete" => [
                        "street" => "FIELD_TO_AUTOCOMPLETE_STREET",
                    ],
                ],
                "phone" => "FIELD_PHONE",
                "sms" => "FIELD_SMS",
            ],
            "name" => [
                "tariffs" => "AUTO_TYPE",
                "time" => "DATE",
                "from" => [
                    "address" => [
                        "street" => "STREET_FROM",
                        "porch" => "PORCH_FROM",
                    ],
                    "comment" => "COMMENT",
                    "wishes" => "ADDITIONAL",
                ],
                "to" => [
                    "address" => [
                        "street" => "STREET_TO",
                    ],
                ],
                "phone" => "PHONE",
            ],
            "inputMask" => "7 (999) 999 99 99",
        ],
        "buttons" => [
            "id" => [
                "sms" => "BUTTON_SMS",
                "calccost" => "BUTTON_CALC_COST",
                "createorder" => "BUTTON_CREATE_ORDER",
            ],
        ],
        "elements" => [
            "id" => [
                "getcost" => "GET_COST",
                "result" => [
                    "info" => "result-message",
                    "calccost" => [
                        "label" => "RESULT_COST_LABEL",
                        "cost" => "RESULT_COST",
                        "time" => "RESULT_TIME",
                        "dist" => "RESULT_DIST",
                        "sumcost" => "RESULT_COST_SUM",
                    ],
                ],
            ],
        ],
    ],
];
