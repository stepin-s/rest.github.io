<?php

/*
 * Точка входа для клиента
 */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once dirname(__FILE__) . '/protected/import_common.php';

$client = new TaxiClient();
$client->processRequest();