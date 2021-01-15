<?php

if (!is_array($_REQUEST) || !array_key_exists('command', $_REQUEST))
  return;

require_once __DIR__ . DIRECTORY_SEPARATOR . "TaxiOrder.php";
TaxiOrder::init();

if ('init' == $_REQUEST['command']) {
  $jsMap = TaxiOrder::loadJs();
  $config = TaxiOrder::getUserConfig();
  $params = TaxiOrder::getUserParams();
  echo json_encode(['params' => $params,  'config' => $config, 'js' => $jsMap]);
}