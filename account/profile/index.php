<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Профиль");

?>
<? $APPLICATION->IncludeComponent('gootax:profile', '', []); ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>