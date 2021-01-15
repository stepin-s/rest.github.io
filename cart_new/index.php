<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle('Корзина');
$APPLICATION->SetPageProperty('title', 'Корзина');
?>

<? $APPLICATION->IncludeComponent(
	"gootax:order", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"COMPOSITE_FRAME_MODE" => "N",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>