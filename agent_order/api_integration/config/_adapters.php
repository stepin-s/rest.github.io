<?php

return array(
    'gootax_test' => array(
        'type'    => 'gootax',
        'options' => array(
            'label'               => 'TaxiLime',
            'ip'                  => 'ca2.gootax.pro',
            'port'                => '8089',
            'key'                 => 'jhgj64urejhdyh34uyherfhrfvhjuthfghfghgfhfg',
			'defaultCity'         => 'Минск',
			'iphoneHackRegistrationTel' => '79000000000',
            'callTarifsFromAPI'   => true,
            'configuratorClass'   => 'Gootax_custom',
			'useSmsAuthorization' => true,
			'tenantid'            => 6395,
            'appid'               => 13753,
			'costCurrency'        => 'руб',
            'dbHost'              => 'localhost',
            'dbLogin'             => '',
            'dbPass'              => '' ,
            'database'            => '',
			'defaultTariffs'    => array(
			),
            'cityData'            => array(
			),
        ),
    ),
);
