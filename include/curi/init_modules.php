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

if ($file_path = CUriModule::find_file($real_uri)) {
  include_once $file_path;
  exit();
}

/*
 * композит и urlrewrite работают через $_SERVER["REQUEST_URI"]
 * если не изменять $_SERVER["REQUEST_URI"] не будуь работать правила в /urlrewrite.php
 * если изменить $_SERVER["REQUEST_URI"] композит будет сохранять файлы в одну папку независимо от url(город|язык)
 * в /urlrewrite изменить массив $arUrlRewrite - вставить SITE_PREFIX к каждому условию
 */
//$_SERVER["REQUEST_URI"] = $real_uri; //для "/bitrix/urlrerite.php"
include_once($_SERVER['DOCUMENT_ROOT'] . '/urlrewrite.php');
if (!defined(CUriModule::URL_REWRITE)) {
    if (false === CUriModule::rewrite_rulles())
        CUriModule::set_error('', 'Ошибка перезаписи url правил!');
}