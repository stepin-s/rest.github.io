<?php

foreach ($arResult["PROPERTY_LIST"] as $propertyID) {
    $arResult['USER_ERRORS'][$propertyID] = array(
        'ID' => $propertyID,
        'ERROR' => str_replace("#PROPERTY_NAME#", intval($propertyID) > 0 ? $arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"] : (!empty($arParams["CUSTOM_TITLE_" . $propertyID]) ? $arParams["CUSTOM_TITLE_" . $propertyID] : GetMessage("IBLOCK_FIELD_" . $propertyID)), GetMessage("IBLOCK_ADD_ERROR_REQUIRED")),
        'ERROR_REPLACE' => str_replace("#PROPERTY_NAME#", intval($propertyID) > 0 ? tr($arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"]) : (!empty($arParams["CUSTOM_TITLE_" . $propertyID]) ? $arParams["CUSTOM_TITLE_" . $propertyID] : GetMessage("IBLOCK_FIELD_" . $propertyID)), GetMessage("IBLOCK_ADD_ERROR_REQUIRED")),
    );
}