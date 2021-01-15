<?php

$GLOBALS['params'] = [
    'host'        => 'https://ca2.gootax.pro:8089/api-site/',
    'apiKey'      => '12ce23vr34r43v243rbvv2t4fd',
    'tenantId'    => '8936',
    'typeVersion' => '1.1.0',
    'cityId'      => '210861',
    'langs'       => ['az', 'en', 'ru'],
    //'tmp'         => __DIR__ . '/../tmp', // Путь к временным файлам
    'tmp'         => dirname(dirname(dirname(__DIR__))) . '/tmp', // Путь к временным файлам

    'email' => [
        'from'       => 'info@taxi111.ru',
        'from_title' => 'Такси 111',
        'subject'    => [
            'ru' => 'Такси 111',
            'en' => 'Taxi 111',
            'az' => 'Taxi 111',

        ],
        'pattern'    => [
            //Телефон поддержки
            'help_phone'     => '',
            //Почта поддержки
            'help_email'     => 'info@taxi111.ru',
            //Сокращенное название организации
            'title'          => 'Такси 111',
            //Адрес портала
            'portal_address' => 'taxi111',
        ],
    ],

    'geoservice' => [
        //        'host'    => 'http://192.168.10.206:3100/v1/',
        'host'    => 'https://geo.gootax.pro/v1/',
        'apiKey'  => '123',
        'lat'     => '40.46',
        'lon'     => '50.05',
        'cityId'  => '210863',
        'typeApp' => 'client',
        'size'    => '30',
        'format'  => 'gootax',
        'radius'  => '100',
    ],

    'points'    => [
        [
            //            'label'  => 'Международный Аэропорт имени Гейдара Алиева',
            'lat'    => '40.462579986412',
            'lon'    => '50.05017222857',
            'cityId' => '210863',
            'city'   => 'Баку',
            'street' => 'Международный Аэропорт имени Гейдара Алиева',
        ],
        [
            //'label' => 'Airport №2',
            'lat'    => '40.462579986412',
            'lon'    => '50.05017222857',
            'cityId' => '210863',
            'city'   => 'Баку',
            'street' => 'Street Test',
        ],
    ],
    'positions' => [
        'driver'  => [
            'lang' => [
                'ru' => 'Водителем такси',
                'en' => 'As driver',
                'az' => 'Sürücü kimi',
            ],
            'class_id' => 1,
        ],
        'courier' => [
            'lang' => [
                'ru' => 'Курьером',
                'en' => 'As courier',
                'az' => 'Kuryer kimi',
            ],
            'class_id' => 28,
        ],
    ],
];