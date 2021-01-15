<?php
$arUrlRewrite=array (
  2 => 
  array (
    'CONDITION' => '#^/services/([\\w\\d-]+)(\\\\?(.*))?#',
    'RULE' => 'ELEMENT_CODE=$1',
    'ID' => '',
    'PATH' => '/services/detail.php',
    'SORT' => 100,
  ),
  1 => 
  array (
    'CONDITION' => '#^/tariffs/([\\w\\d-]+)(\\\\?(.*))?#',
    'RULE' => 'ELEMENT_CODE=$1',
    'ID' => '',
    'PATH' => '/tariffs/detail.php',
    'SORT' => 100,
  ),
  0 => 
  array (
    'CONDITION' => '#^/news/([\\w\\d-]+)(\\\\?(.*))?#',
    'RULE' => 'ELEMENT_CODE=$1',
    'ID' => '',
    'PATH' => '/news/detail.php',
    'SORT' => 100,
  ),
  3 => 
  array (
    'CONDITION' => '#^/goods/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/goods/index.php',
    'SORT' => 100,
  ),
);
