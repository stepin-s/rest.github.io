<?php

abstract class TaxiOrder {

  private static $instance;
  private static $internalConfigFile = 'config/main.php';
  private static $internalConfigParams = 'params.php';
  private static $publicConfigFile = 'config/main.json';
  private static $config = [];

  public static function init() {
    self::loadConfig();
  }

  public static function getConfig() {
    return self::$config;
  }

  private function loadConfig($updateJSON = false) {
    $params = require __DIR__ . DIRECTORY_SEPARATOR . self::$internalConfigParams;
    if (!$params)
      die('config internal error: ' . __FILE__ . __LINE__);

    foreach ($params as $i => $param) {
      if ((bool) $param['active']) {
        $config = [
          "config" => require_once $param['configPath'],
          "params" => $param["userParams"],
        ];
        break;
      }
    }

    self::$config = $config;
  }

  public static function Config($key) {
    $found = 0;
    $keys = explode(".", $key);
    $result = self::$config["config"];

    while (array_key_exists(current($keys), $result)) {
      $result = $result[current($keys)];
      $found++;
      next($keys);
    }

    return ($found === count($keys)) ? $result : null;
  }

}
