<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");


$sect = new GetItems();
$sect->sectionsBitrix();

class GootaxAdapterValidator {
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
    private function itemsRequest() 
    {
        $optionsSections= ['provider_id' => GootaxAdapterValidator::PROVIDER_ID, 'current_time' => time()];

        $resultSections = $this->send('get','v1/get_provider_items', $optionsSections);

        return $resultSections;
    }

    private function itemRequest($id) 
    {
        $optionsItem= ['item_id' => $id, 'current_time' => time()];

        $resultItem = $this->send('get','v1/get_item', $optionsItem);

        return $resultItem;
    }

    public function qq()
    {

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
                echo $section.'.New ID: '.$PRODUCT_ID.'(XML_ID =  '.$section["id"].')<br>';
            }
            else{
                echo $section.'.Error: '.$bs->LAST_ERROR.'<br>';
            }

            $res = CIBlockSection::GetList(array(), array('IBLOCK_ID' => 13, 'XML_ID' => $section['id'], 'SITE_ID' => "s1"));
            $sectionId = $res->Fetch();
            echo('<br>$sectionId["ID"] <br>');

            $this->itemsBitrix($section["items"], $sectionId["ID"], $section['id']);

        }

    }

    public function itemsBitrix($items, $section, $sectionId) 
    {

        //грузим с пу детальную инфу по каждому товару 
        foreach ($items as $item){
            $detail = $this->itemRequest($item["id"]);
            var_dump($detail);

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

            var_dump($PROP['TYPES']);


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
                echo $item.'.New ID: '.$PRODUCT_ID.'(XML_ID =  '.$item["id"].')<br>';
            }
            else{
                echo $item.'.Error: '.$bs->LAST_ERROR.'<br>';
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
            "tenantid: ". GootaxAdapterValidator::TENANT_ID,
            "appid: " . GootaxAdapterValidator::APPID,
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
        return "https://". GootaxAdapterValidator::IP . ":" . GootaxAdapterValidator::PORT . "/{$method}";
        // return "https://". GootaxAdapterValidator::IP . ":" . GootaxAdapterValidator::PORT ."/{$method}";
    }

    private function getSignature($params)
    {
        return MD5($params . GootaxAdapterValidator::KEY);
    }


}
