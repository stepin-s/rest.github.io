<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
CModule::IncludeModule('iblock');

class BitrixOrder {

  public $hasErrors = false;
  
  public function findTariffs() {
    $arSelect = Array(
      "ID",
      "NAME",
      "CODE",
      "PROPERTY_MINIMAL_COST",
      "PROPERTY_LANDING_COST",
      "PROPERTY_INCLUDE_KM_OR_MIN",
      "PROPERTY_KM_OR_MIN_COST",
      "PROPERTY_CHOOSE_KM_OR_MIN"
    );
    $ar_city = CUriCity::get_instance()->get_current_element();
    $arFilter = Array("IBLOCK_ID" => CUriLang::get_instance()->get_iblock_id("tariffs"), "PROPERTY_CITY" => $ar_city["id"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 10), $arSelect);
    $ar_tariffs = [];
    while ($arFields = $res->fetch()) {
      $ar_tariffs[] = [
        "id" => $arFields["ID"],
        "label" => $arFields["NAME"],
        "minimal_cost" => $arFields["PROPERTY_MINIMAL_COST_VALUE"],
        "landing_cost" => $arFields["PROPERTY_LANDING_COST_VALUE"],
        "include_km_or_min" => $arFields["PROPERTY_INCLUDE_KM_OR_MIN_VALUE"],
        "choose_km_or_min" => $arFields["PROPERTY_CHOOSE_KM_OR_MIN_VALUE"],
        "km_or_min_cost" => $arFields["PROPERTY_KM_OR_MIN_COST_VALUE"],
      ];
    }

    return $ar_tariffs;
  }

  public function createOrder($params) {
    $el = new CIBlockElement;

    $PROP = array(
      90 => $params["fromStreet"],
      93 => $params["fromPorch"],
      95 => $params["toStreet"],
      99 => $params["clientName"],
      100 => $params["phone"],
      101 => $params["priorTime"],
      102 => $params["tariffGroupId"],
      103 => implode(", ", $_POST["ADDITIONAL"]),
      104 => $params["comment"],
    );

    $arLoadProductArray = Array(
      //"MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
      "IBLOCK_SECTION_ID" => false, // элемент лежит в корне раздела
      "IBLOCK_ID" => 31,
      "PROPERTY_VALUES" => $PROP,
      "NAME" => "Новый заказ",
      "ACTIVE" => "Y", // активен
    );

    if ($ID = $el->Add($arLoadProductArray)) {
      return $ID;
    } else {
      $this->hasErrors = true;
      $this->errorCode = 400;
      $this->errorsInfo = ['summaryText' => $el->LAST_ERROR];
    }
  }

}
