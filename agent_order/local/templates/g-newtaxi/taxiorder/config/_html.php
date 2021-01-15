<?php

return [
    'elements' => [
        'id' => [
            'orderForm' => 'element-order-form',
            'map' => 'map',
            'findMe' => 'element-find-me',
        ],
    ],
    'inputs' => [
        'id' => [
            'userPhone' => 'input-user-phone',
            'userName' => 'input-user-name',
            'orderTime' => 'input-order-time',
            'wishes' => 'input-wishes',
            'smsCode' => 'input-sms-code',
            'tariffs' => 'input-tariffs',
            'from' => [
                'autocomplete' => 'input-from-autocomplete',
                'city' => 'input-from-city',
                'street' => 'input-from-street',
                'house' => 'input-from-house',
                'housing' => 'input-from-housing',
                'porch' => 'input-from-porch',
                'comment' => 'input-from-comment',
            ],
            'to' => [
                'autocomplete' => 'input-to-autocomplete',
                'city' => 'input-to-city',
                'street' => 'input-to-street',
                'house' => 'input-to-house',
                'housing' => 'input-to-housing',
                'porch' => 'input-to-porch',
            ],
        ],
    ],
    'buttons' => [
        'id' => [
            'smsCode' => 'button-sms-code',
            'createOrder' => 'button-create-order',
            'rejectOrder' => 'button-reject-order',
        ],
    ],
];