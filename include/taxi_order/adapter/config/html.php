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
                        "city" => "FIELD_FROM_CITY",
                        "street" => "FIELD_FROM_STREET",
                        "house" => "FIELD_FROM_HOUSE",
                        "housing" => "FIELD_FROM_HOUSING",
                        "porch" => "FIELD_FROM_PORCH",
                    ],
                    "comment" => "FIELD_FROM_COMMENT",
                    "wishes" => "FIELD_FROM_WISHES",
                ],
                "to" => [
                    "address" => [
                        "city" => "FIELD_TO_CITY",
                        "street" => "FIELD_TO_STREET",
                        "house" => "FIELD_TO_HOUSE",
                        "housing" => "FIELD_TO_HOUSING",
                        "porch" => "FIELD_TO_PORCH",
                    ],
                    "autocomplete" => [
                        "street" => "FIELD_TO_AUTOCOMPLETE_STREET",
                    ],
                ],
                "phone" => "FIELD_PHONE",
                "clientname" => "FIO",
                "sms" => "FIELD_SMS",
            ],
            "name" => [
                "time" => "TIME",
                "tariffs" => "AUTO_TYPE"
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
                "map" => "MAP",
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
