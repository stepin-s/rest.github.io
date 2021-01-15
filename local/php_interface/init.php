<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/curi/functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/curi/CUriModule.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/curi/lang/CUriLang.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/curi/city/CUriCity.php";
//require_once($_SERVER["DOCUMENT_ROOT"]."/api_integration/config/_adapters.php");
require_once $_SERVER["DOCUMENT_ROOT"] . '/api_integration/protected/import_common.php';
require_once $_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/src/client/profile.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/src/gootaxbase.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/src/tenat/tenat.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/src/geo/gootaxgeo.php";

use Bitrix\Main\Diag;

AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("CUriCache", "OnAfterIBlockElementAddHandler"));
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array("CUriCache", "OnAfterIBlockElementUpdateHandler"));
AddEventHandler("iblock", "OnAfterIBlockElementDelete", Array("CUriCache", "OnAfterIBlockElementDeleteHandler"));

class CUriCache {

    function UpdateCacheElements($arFields, $action = array()) {
        $CLang = CUriLang::get_instance();
        $CCity = CUriCity::get_instance();

        $ar_iblock = CUriModule::get_iblock_info($arFields["IBLOCK_ID"]);
        if (!$ar_iblock)
            return;
        //CUriCity::set_error(__METHOD__, "Инфоблок не найден!");

        if ($ar_iblock["code"] == $CLang->get_iblock_type()) {
            $ar_elements = $CLang->get_elements(false, $arFields["IBLOCK_ID"]);
            if (!check_array($ar_elements))
                CUriLang::set_error(__METHOD__, "Элементы не найдены!");

            if ($action["add"] && !array_key_exists($arFields["CODE"], CUriModule::get_dirs()))
                CUriLang::set_error(__METHOD__, "Инфоблок не найден!");

            elseif ($action["add"] && !$CLang->create_element($arFields["CODE"]))
                CUriLang::set_error(__METHOD__, "Ошибка создания директории кэша!");

            if (!$CLang->set_element_code($ar_iblock["section"]))
                CUriLang::set_error(__METHOD__, "Элемент не установлен!");

            if (!$CLang->update_cache($ar_elements))
                CUriLang::set_error(__METHOD__, "Ошибка обновления кэша!");

            return true;
        }
        elseif ($ar_iblock["code"] == $CCity->get_iblock_type()) {
            $ar_elements = $CCity->get_elements(false, $arFields["IBLOCK_ID"]);
            if (!check_array($ar_elements))
                CUriCity::set_error(__METHOD__, "Элементы не найдены!");

            if (!$CCity->CLang->set_element_code($ar_iblock["section"]))
                CUriCity::set_error(__METHOD__, "Элемент не установлен!");

            if (!$CCity->update_cache($ar_elements))
                CUriCity::set_error(__METHOD__, "Ошибка обновления кэша!");
        }
    }

    function OnAfterIBlockElementAddHandler(&$arFields) {
        $action["add"] = true;
        self::UpdateCacheElements($arFields, $action);

        $db_iblock = CIBlock::GetByID($arFields['IBLOCK_ID']);
        $iblock = $db_iblock->GetNext();
        if($iblock['CODE'] == 'reviews_form') {
            $adminEmail = COption::GetOptionString('main', 'email_from', 'default@admin.email');
            $EVENT = 'REVIEW_ADD';
            $arEventFields = array(
                'EMAIL_TO' => $adminEmail,
                'FIO' => $arFields["PROPERTY_VALUES"][29],
                'EMAIL_PHONE' => $arFields["PROPERTY_VALUES"][30],
                'REVIEW' => $arFields["PROPERTY_VALUES"][31],
                'MARK' => $arFields["PROPERTY_VALUES"][32],
                'CITY' => $arFields["PROPERTY_VALUES"][33][0],
            );
            CEvent::Send($EVENT, SITE_ID, $arEventFields);
        }
        if($iblock['CODE'] == 'call_me_form') {
            $adminEmail = COption::GetOptionString('main', 'email_from', 'default@admin.email');
            $EVENT = 'CALLBACK_FORM';
            $arEventFields = array(
                'EMAIL_TO' => $adminEmail,
                'FIO' => $arFields["PROPERTY_VALUES"][21],
                'PHONE' => $arFields["PROPERTY_VALUES"][22],
                'WHEN_CALL' => $arFields["PROPERTY_VALUES"][23],
            );
            CEvent::Send($EVENT, SITE_ID, $arEventFields);
        }
        if($iblock['CODE'] == 'contacts_form') {
            $adminEmail = COption::GetOptionString('main', 'email_from', 'default@admin.email');
            $EVENT = 'FEEDBACK_FORM';
            $arEventFields = array(
                'EMAIL_TO' => $adminEmail,
                'AUTHOR' => $arFields["PROPERTY_VALUES"][25],
                'AUTHOR_EMAIL' => $arFields["PROPERTY_VALUES"][26],
                'TEXT' => $arFields["PROPERTY_VALUES"][27],
            );
            CEvent::Send($EVENT, SITE_ID, $arEventFields);
        }
    }

    function OnAfterIBlockElementUpdateHandler(&$arFields) {
        $action["update"] = true;
        self::UpdateCacheElements($arFields, false);
    }

    function OnAfterIBlockElementDeleteHandler($arFields) {
        $action["delete"] = true;
        self::UpdateCacheElements($arFields);
    }

}

function getApiItems() {

    $sect = new GetItems();
    $sect->sectionsBitrix();
    return "getApiItems();";
}

class GootaxAdapterValidatorNew {
    const  IP        = 'ca2.gootax.pro';
    const  PORT      = '8089';
    const  KEY       = '12ce23vr34r43v243rbvv2t4fd';
    const  APPID     = "13703";
    const  TENANT_ID = "8936";
    const  PROVIDER_ID = "73";
}
/**
 * Получение категорий
 */
class GetItems
{

    private $config = [];

    public function __construct()
    {
        $taxiConfig = new TaxiConfig();

        $this->config = current($taxiConfig->getAdaptersConfig());
    }

    private function itemsRequest()
    {

        $optionsSections= ['provider_id' => $this->config['options']['provider_id'], 'current_time' => time()];

        $resultSections = $this->send('get','v1/get_provider_items', $optionsSections);

        return $resultSections;
    }

    private function itemRequest($id)
    {
        $optionsItem= ['item_id' => $id, 'current_time' => time()];

        $resultItem = $this->send('get','v1/get_item', $optionsItem);

        //Diag\Debug::writeToFile($this->config, $varName = "", $fileName = "");
        return $resultItem;
    }


    public function sectionsBitrix()
    {
        //подгружаем товары с пу
        $res = $this->itemsRequest();
        $items = $res;

        CModule::IncludeModule("iblock");

        $infoblock = 13; //товары
        $rs_Section = CIBlockSection::GetList(array('left_margin' => 'asc'), array('IBLOCK_ID' => $infoblock));
        while ( $ar_Section = $rs_Section->Fetch() ) {
            $ar_Resu[] = array(
                'ID' => $ar_Section['ID'],
            );
        }

        foreach ($ar_Resu as $section) {
            CIBlockSection::Delete($section["ID"]);
        }

        //подгружаем непустые категории с пу
        $sections = $items["result"]["sections"];


        foreach ($sections as $section){
            $bs = new CIBlockSection;
            $status = '2';

            $PROP = array();
            $PROP['SECTION_ID'] = $section['id'];

            $arFields = Array(
                "ACTIVE" => 'Y',
                "IBLOCK_ID" => 13,
                "NAME" => $section['name'],
                "CODE" => $section["id"],
                "XML_ID" => $section['id'],
                "PROPERTY_VALUES"=> $PROP,
            );
            if($PRODUCT_ID = $bs->Add($arFields, false, false, false)){
                //echo $section.'.New ID: '.$PRODUCT_ID.'(XML_ID =  '.$section["id"].')<br>';
            }
            else{
                //echo $section.'.Error: '.$bs->LAST_ERROR.'<br>';
            }

            $res = CIBlockSection::GetList(array(), array('IBLOCK_ID' => 13, 'XML_ID' => $section['id'], 'SITE_ID' => "s1"));
            $sectionId = $res->Fetch();
            //echo('<br>$sectionId["ID"] <br>');

            $this->itemsBitrix($section["items"], $sectionId["ID"], $section['id']);

        }

    }

    public function itemsBitrix($items, $section, $sectionId)
    {

        //грузим с пу детальную инфу по каждому товару
        foreach ($items as $item){
            $detail = $this->itemRequest($item["id"]);
            //var_dump($detail);

            $bs = new CIBlockElement;
            $status = '2';

            $PROP = array();
            $PROP['IMAGE'] = $detail['result']['images'][0];

            $PROP['BADGE'] = $detail['result']['badge'];
            $PROP['ITEM_ID'] = $detail['result']['id'];
            $PROP['SECTION_ID'] = $sectionId;

            $types =  $detail['result']['types'];
            $i = 0;
            foreach ($types as $type){
                $PROP['TYPES'][$i] = json_encode($type);
                $i++;
            }

            $dops =  $detail['result']['additive'];
            $i = 0;
            foreach ($dops as $dop){
                $PROP['DOPS'][$i] = json_encode($dop);
                $i++;
            }

            //var_dump($PROP['TYPES']);


            $arFields = Array(
                "IBLOCK_SECTION_ID" => $section,
                "ACTIVE" => 'Y',
                "IBLOCK_ID" => 13,
                "NAME" => $item['name'],
                "XML_ID" => $item['id'],
                "DETAIL_TEXT" => $item['description'],
                ///"DETAIL_PICTURE" => $item['images'],
                "PROPERTY_VALUES"=> $PROP,
            );

            if($PRODUCT_ID = $bs->Add($arFields, false, false, false)){
                //echo $item.'.New ID: '.$PRODUCT_ID.'(XML_ID =  '.$item["id"].')<br>';
            }
            else{
                //echo $item.'.Error: '.$bs->LAST_ERROR.'<br>';
            }
        }
    }



    public function send($type,$method, $params = '')
    {
        $lang = 'ru';;
        if (is_array($params)) {
            $paramsString = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        }

        if ($type === 'post'){
            $url = $this->getUrl($method);
        } else {
            $url = $this->getUrl($method)  . "?" . $paramsString;
        }

        // var_dump($url);

        $signature = $this->getSignature($paramsString);
        $headers = [
            'typeclient: web',
            "lang: {$lang}",
            "tenantid: ". $this->config['options']['tenantid'],
            "appid: " . $this->config['options']['appid'],
            "signature: {$signature}",
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, $type === 'post' ? true:false);
        if($type === 'post'){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $errorCode = curl_errno($ch);
        curl_close($ch);
        return ($errorCode == CURLE_OK) ? json_decode($result,true) : false;
    }

    private function getUrl($method)
    {
        $port = isset($this->config['options']['port']) ? ":". $this->config['options']['port'] : '';
        return "https://". $this->config['options']['ip'] . $port ."/{$method}";
        // return "https://". GootaxAdapterValidatorNew::IP . ":" . GootaxAdapterValidatorNew::PORT ."/{$method}";
    }

    private function getSignature($params)
    {
        return MD5($params . $this->config['options']['key']);
    }


}