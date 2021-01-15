<?php

/*
 * ! Карта подгрузки JS файлов и библиотек
 */
return array(
    /*
     * Для работы с АПИ используем путь относительно корня сайта, 
     * для других же будет использовать путь относительно темы
     */
    '/agent_order/api_integration/assets/js/lib/TaxiDataStore.js',
    '/agent_order/api_integration/assets/js/components/TaxiCustomCarComponent.js',
    '/agent_order/api_integration/assets/js/components/TaxiRouteComponent.js',
    '/agent_order/api_integration/assets/js/components/cost/TaxiCost.js',
    '/agent_order/api_integration/assets/js/TaxiMethod_createOrder.js',
    '/agent_order/api_integration/assets/js/TaxiErrorsInfo.js',
    '/agent_order/api_integration/assets/js/TaxiClient.js',
    '/agent_order/api_integration/assets/js/TaxiMethod.js',
    '/agent_order/api_integration/assets/js/TaxiBitrixModalWindow.js',
    '/agent_order/api_integration/assets/js/TaxiOrderProcess.js',
    '/agent_order/api_integration/assets/js/bitrix/TaxiBitrixOrderProcess.js',
    /*
     * Сторонние библиотеки
     */
    '/agent_order/api_integration/assets/js/bitrix/taxi/vendors/mobileLib.js',
    /*
     * Базовые объекты
     */
    '/agent_order/api_integration/assets/js/bitrix/taxi/system/baseObject.js',
    /*
     * Карты
     */
    '/agent_order/api_integration/assets/js/bitrix/taxi/maps/addYandexMap.js',
    '/agent_order/api_integration/assets/js/bitrix/taxi/maps/addGoogleMap.js',
    '/agent_order/api_integration/assets/js/bitrix/taxi/maps/addYandexGeocoder.js',
    '/agent_order/api_integration/assets/js/bitrix/taxi/maps/addGoogleGeocoder.js',
    /*
     * Библиотека
     */
    '/agent_order/api_integration/assets/js/bitrix/taxi/lib/YandexSuggestCaller.js',
    '/agent_order/api_integration/assets/js/bitrix/taxi/lib/TaxiSuggestCaller.js',
    /*
     * Адаптеры
     */
    '/agent_order/api_integration/assets/js/bitrix/taxi/adapters/OTaxiApi.js',
    /*
     * Основное
     */
    '/agent_order/api_integration/assets/js/bitrix/top-panel.js',
    '/agent_order/api_integration/assets/js/bitrix/other.js',
    '/agent_order/api_integration/assets/js/bitrix/taxi_init.js',    
    /*
     * Библиотечные скрипты:
     */
    '<SITE_TEMPLATE_PATH>/agent_order/js/ru/colorbox/jquery.colorbox-min.js',
    '<SITE_TEMPLATE_PATH>/agent_order/js/ru/jquery.maskedinput.min.js',
    '<SITE_TEMPLATE_PATH>/agent_order/bootstrap/js/bootstrap.min.js',
    '<SITE_TEMPLATE_PATH>/agent_order/bootstrap/js/bootstrap-tab.js',
    '<SITE_TEMPLATE_PATH>/agent_order/bootstrap/js/html5shiv.js',
);
