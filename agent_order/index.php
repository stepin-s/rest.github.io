<?
SetCookie("CITY_GOOTAX_ID","34712");
SetCookie("CITY_LAT",'43.581509');
SetCookie("CITY_LON",'39.722882');

SetCookie("USER_LANG","ru");

require_once $_SERVER["DOCUMENT_ROOT"]."/agent_order/include/curi/functions.php";
?>
<?php
// Turn off all error reporting
error_reporting(0);
?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <title><? //$APPLICATION->ShowTitle(); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="/agent_order/assets/jquery-2.2.4.min.js"></script>
        <link href="https://fonts.googleapis.com/css?family=PT+Sans+Narrow:700|PT+Sans:400,700&amp;subset=cyrillic-ext"
              rel="stylesheet">
        <script src="assets/tail.datetime.min.js"></script>   
        <script src="assets/tail.datetime-ru.js"></script>    
        <link rel="stylesheet" href="/agent_order/assets/tail.datetime-default-blue.min.css">
        <link rel="stylesheet" href="/agent_order/assets/screen.css">
        <style type="text/css">
            html, body, .vk-order{
                position: relative;
                height: 100%;
                width: 100%;
            }
        </style>
    </head>
    <body style="background:#000">

    <script src="https://api-maps.yandex.ru/2.1/?load=package.full&lang=ru-RU"></script>
    <script type="text/javascript">
        window.geoservice = 'yandex';
    </script>

    <script>
        window.order = "";
        window.crew = "";
        window.source = "";
        window.order_city = "";
    </script>

    <? include "assets/taxiorder.php"; ?>
    <script type="text/javascript" src="/agent_order/include/taxiorder/js/FactoryTaxiOrder.js"></script>
    <!-- <script type="text/javascript" src="/agent_order/assets/template.js"></script> -->
    <script type="text/javascript" src="/agent_order/local/templates/g-newtaxi/taxiorder/js/template.js"></script>
    <!-- <script type="text/javascript" src="/agent_order/local/templates/g-newtaxi/taxiorder/js/scripts.js"></script> -->
    <script type="text/javascript" src="/agent_order/assets/scripts.js"></script>
    </body>
    </html>