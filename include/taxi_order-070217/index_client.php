<?php

if (!is_array($_REQUEST) || !array_key_exists('command', $_REQUEST))
  return;

require_once __DIR__ . DIRECTORY_SEPARATOR . "TaxiOrder.php";
TaxiOrder::init();

if ('getConfig' == $_REQUEST['command'])
  echo json_encode(TaxiOrder::getConfig());
elseif ('getParams' == $_REQUEST['command']) {
  $config = TaxiOrder::getConfig();
  if (!$config['params']['map'])
    unset($config['config']['map']);
  echo json_encode($config);
} elseif ('getOrderConfig' == $_REQUEST['command']) {
  $config = TaxiOrder::getConfig();
  unset($config['params']);
  if (!$_REQUEST['map'])
    unset($config['map']);

  echo json_encode($config);
}