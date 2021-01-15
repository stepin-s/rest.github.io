<?php

if (!class_exists("CUriModule"))
  return;

require_once "CUriLang.php";

$CLang = CUriLang::get_instance();

if (!$CLang->is_active()) {
  unset($CLang);
  return;
}

$CLang->define_ajax_query();

CUriModule::register_module($CLang);