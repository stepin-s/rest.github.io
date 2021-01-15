<?php

include_once __DIR__ . '/models/GeoService.php';


header('Content-Type: application/json');

$code = 'OK';
$response = [];

$text = $_POST['text'] ?: '';
$lang = $_POST['lang'] ?: 'en';
$lat = $_POST['lat'] ?: null;
$lon = $_POST['lon'] ?: null;

$model = new GeoService();
$response = $model->autocomplete($text, $lang, $lat, $lon);

echo json_encode(compact('code', 'response'));