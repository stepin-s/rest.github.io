<?php

if (!class_exists("CUriModule"))
  return;

require_once "CUriCity.php";

$CCity = CUriCity::get_instance();

if (!$CCity->is_active()) {
  unset($CCity);
  return;
}

$CCity->define_ajax_query();

CUriModule::register_module($CCity);
