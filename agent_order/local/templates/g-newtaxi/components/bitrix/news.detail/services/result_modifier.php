<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$lang =  '_' . $_COOKIE['USER_LANG'];
if ($_COOKIE['USER_LANG'] != 'ru') {
    $arResult["NAME"] = $arResult['PROPERTIES']['NAME' . $lang]['~VALUE'];
    $arResult["PREVIEW_TEXT"] = $arResult['PROPERTIES']['PREVIEW_TEXT' . $lang]['~VALUE']['TEXT'];
    $arResult["DETAIL_TEXT"] = $arResult['PROPERTIES']['DETAIL_TEXT' . $lang]['~VALUE']['TEXT'];
}

$APPLICATION->SetTitle($arResult["NAME"]);
$APPLICATION->SetPageProperty("title", $arResult["NAME"]);