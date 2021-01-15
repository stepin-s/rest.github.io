<?php

return array(
    'gootax_test' => array(
        'type'    => 'gootax',
        'options' => array(
            'label'               => 'gootax Euro',
            'ip'                  => 'ca.uatgootax.ru',
            //'port'                => '80',
            'key'                 => '123123',
            'iphoneHackRegistrationTel' => '79000000000',
            'callTarifsFromAPI'   => true,
            'configuratorClass'   => 'Gootax_custom',
            'useSmsAuthorization' => true,
            'tenantid'            => 832,
            'appid'               => 360,
            'provider_id'         => 38,
            'costCurrency'        => 'руб',
            'dbHost'              => 'localhost',
            'dbLogin'             => '',
            'dbPass'              => '' ,
            'database'            => '',
            'cityId'              => 26068
        ),
    ),
);

