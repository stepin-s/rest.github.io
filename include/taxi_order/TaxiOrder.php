<?php

abstract class TaxiOrder
{

    private static $configParams = 'params.php';
    private static $userConfig = [];
    private static $params = [];
    private static $jsMap = [
        __DIR__ . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "TaxiOrder.js"
    ];

    public static function init()
    {
        self::loadConfig();
    }

    private function loadConfig()
    {
        self::$params = require __DIR__ . DIRECTORY_SEPARATOR . self::$configParams;
        if (!self::$params)
            die('config internal error: ' . __FILE__ . __LINE__);

        $dir = self::defineOrderDir(self::$params);
        self::$userConfig = require_once($dir . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "main.php");
        if (!self::$params['map']) unset(self::$userConfig['map']);
    }

    private static function defineOrderDir($params)
    {
        if (!array_key_exists('adapter', $params) || !$params['adapter'])
            return __DIR__ . DIRECTORY_SEPARATOR . "bitrix";
        elseif ($params['adapter'] && array_key_exists('id', $params['adapter']))
            return __DIR__ . DIRECTORY_SEPARATOR . "adapter";
    }

    private static function getJsInstances($dir)
    {
        $jsMap[0] = $dir . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "OrderCommon.js";
        $jsMap[1] = self::$params['map'] ? $dir . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "OrderMap.js" : $dir . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "Order.js";
        //$jsMap[2] = $dir . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "OrderTemplate.js";

        return $jsMap;
    }

    public static function loadJs()
    {
        $dir = self::defineOrderDir(self::$params);
        $jsMap = self::getJsInstances($dir);
        $allJs = array_merge(self::$jsMap, $jsMap);

        foreach ($allJs as $i => $js)
            $allJs[$i] = str_replace("\\", "/", substr_replace($js, "", 0, strlen($_SERVER["DOCUMENT_ROOT"])));

        return $allJs;
    }

    public function getUserConfig() {
        return self::$userConfig;
    }

    public function getUserParams() {
        return self::$params;
    }

    public static function Config($key)
    {
        $found = 0;
        $keys = explode(".", $key);
        $result = self::$userConfig;

        while (array_key_exists(current($keys), $result)) {
            $result = $result[current($keys)];
            $found++;
            next($keys);
        }

        return ($found === count($keys)) ? $result : null;
    }

}
