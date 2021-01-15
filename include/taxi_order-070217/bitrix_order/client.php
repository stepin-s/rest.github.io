<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'BitrixOrder.php';
$bitrixOrder = new BitrixOrder();

if (!method_exists($bitrixOrder, $_GET['command']))
  return;

$params = (is_array($_POST) && array_key_exists('params', $_POST)) ? $_POST['params'] : [];

$result = call_user_func_array([$bitrixOrder, $_GET['command']], [$params]);

if (!$bitrixOrder->hasErrors) {
  $response = [
    'result' => $result,
    'status' => 1,
    'hasErrors' => false,
  ];
}
else {
  $response = [
    'result' => null,
    'status' => 0,
    'hasErrors' => true,
    'errorCode' => $bitrixOrder->errorCode,
    'errorsInfo' => $bitrixOrder->errorsInfo
  ];
}
echo json_encode($response);
unset($bitrixOrder);
