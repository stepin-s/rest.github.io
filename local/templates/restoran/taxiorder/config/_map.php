<?php

define('DR', '/');

return [
    'yandex' => [
		'coords' => (array_key_exists('CITY_LAT', $_COOKIE) && array_key_exists('CITY_LON', $_COOKIE))?[$_COOKIE['CITY_LAT'], $_COOKIE['CITY_LON']]:[],
        'zoom' => 11,
        'icon' => [
            'from' => [
                'iconLayout' => 'default#image',
                'iconImageHref' => SITE_TEMPLATE_PATH . DR . 'taxiorder' . DR . 'images' . DR . 'i' . DR . 'a_icon.png',
                'iconImageSize' => [24, 27],
                //'iconImageOffset' => [12, 27], // point of the icon which will correspond to marker's location'
            ],
            'to' => [
                'iconLayout' => 'default#image',
                'iconImageHref' => SITE_TEMPLATE_PATH . DR . 'taxiorder' . DR . 'images' . DR . 'i' . DR . 'b_icon.png',
                'iconImageSize' => [24, 27],
                //'iconImageOffset' => [12, 27],
            ],
            'freeCar' => [
                'iconLayout' => 'default#image',
                'iconImageHref' => SITE_TEMPLATE_PATH . DR . 'taxiorder' . DR . 'images' . DR . 'i' . DR . 'green_car.png',
                'iconImageSize' => [24, 27],
                //'iconImageOffset' => [12, 27],
            ],
            'busyCar' => [
                'iconLayout' => 'default#image',
                'iconImageHref' => SITE_TEMPLATE_PATH . DR . 'taxiorder' . DR . 'images' . DR . 'i' . DR . 'red_car.png',
                'iconImageSize' => [24, 27],
                //'iconImageOffset' => [12, 27],
            ],
        ],
        'RouteLineStyle' => [
            'strokeColor' => '4e4e4e',
            'opacity' => 0.9
        ],
        'LineStyle' => [
            [
                'fillColor' => 'black',
                'strokeColor' => 'black',
                //'fillOpacity' => 0.15
                //'weight' => 9,
            ],
            [
                'fillColor' => 'white',
                'strokeColor' => 'white',
                //'fillOpacity' => 0.8
                //'weight' => 6,
            ],
            [
                'fillColor' => '#4e4e4e',
                'strokeColor' => '#4e4e4e',
                //'fillOpacity' => 1
                //'weight' => 2,
            ],
        ],
        'MissingLineStyle' => [
            [
                'color' => 'black',
                'opacity' => 0.15,
                'weight' => 7,
            ],
            [
                'color' => 'white',
                'opacity' => 0.6,
                'weight' => 4,
            ],
            [
                'color' => 'gray',
                'opacity' => 0.8,
                'weight' => 2,
                'dashArray' => '7,12'
            ],
        ],
    ],
];