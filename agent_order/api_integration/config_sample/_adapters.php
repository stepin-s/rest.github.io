<?php

return array(
    'tester_krasnoyarsk' => array(
        'type' => 'tester',
        'options' => array(
            'label' => 'Красноярск Мой город - http://2212212.ru',
            'defaultCity' => 'Красноярск',
            'onlyCarModelInfo' => true,
            //'useSmsAuthorization' => true,
            // параметры для подключения к такси-мастеру
            'ip' => '',
            'port' => '',
            'apiKey' => '',
			      'rejectOrderStateIdWithCar'    => '',
            'rejectOrderStateIdWithoutCar' => '',
        ),
    ),
);

