<?php

if (strpos($_SERVER['REQUEST_URI'], "/bitrix") === 0)
  return;

require_once "functions.php";
require_once "CUriModule.php";
require_once "lang/init.php";
require_once "city/init.php";

if (!CUriModule::has_register_modules())
  return;

CUriModule::init_modules();

CUriModule::$seo_uri = $uri = $_SERVER["REQUEST_URI"];

$real_uri = CUriModule::get_real_uri($uri);

if ($uri == $real_uri)
  return;

$_SERVER["REQUEST_URI"] = $real_uri; //для "/bitrix/urlrerite.php"

if ($file_path = CUriModule::find_file($real_uri)) {
  include_once $file_path;
  exit();
}