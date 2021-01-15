<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

/*
 * При успешной отправке заказа через на клиент - запишем в БД битрикса этот
 * заказ через АПИ
 */

function shit_sduwe38328dsj()
{
    /*
     * !!! ХО ХО ХО !!!
     *
     * если подключить битрикс - пролог файл так: и тут
     *
     * require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
     *
     * то битрикс не найдет собственные классы!! , а если выше, то выдаст кучу предупреждений, если уже был где-то подкчючен !
     * поэтому и этот костыль
     */
    $map = array(
        'toCity' => 'toCity',
        'fromCity' => 'fromCity',
        'fromStreet' => 'from_street',
        'toStreet' => 'to_street',
        'priorTime' => 'time',
        'fromHouse' => 'from_house',
        'toHouse' => 'to_house',
        'fromHousing' => 'from_housing',
        'toHousing' => 'to_housing',
        'fromPorch' => 'from_porch',
        'toPorch' => 'to_porch',
        'clientName' => 'fio',
        'phone' => 'phone',
        'customCar' => 'type_avto',
        'comment' => 'comment',
    );
    $inParams = array(
        'dop' => '',
    );
    foreach ($map as $paramInCommand => $paramInBitrix) {
        $inParams[$paramInBitrix] = isset($_GET[$paramInCommand]) ? trim($_GET[$paramInCommand]) : null;
        $value = $inParams[$paramInBitrix];
        $value = urldecode($value);
        $inParams[$paramInCommand] = $value;
    }
    foreach ($_GET as $key => $value){
        $inParams[$key] = $value;
    }

    if (CModule::IncludeModule("iblock")) {

        $order = new CIBlockElement();

        $dop = array();
        if ($inParams['dop']) {
            $array_dop = explode('-', $inParams['dop']);
            $db_prop_xml = CIBlockPropertyEnum::GetList(false, array('CODE' => 'DOP'));
            foreach ($array_dop as $value) {
                $db_prop_xml = CIBlockPropertyEnum::GetList(false, array('CODE' => 'DOP', 'XML_ID' => $value));
                if ($prop_xml = $db_prop_xml->GetNext()) {
                    $dop[] = array('VALUE' => $prop_xml['ID']);
                }
            }
        }

        $bitrixProperties = array(
            'FROM' => $inParams['from_street'] ? $inParams['from_street'] : '---',
            'TO' => $inParams['to_street'],
            'DATA' => $inParams['time'],
            'DATE' => $inParams['time'],
            'FROM_HOUSE' => $inParams['from_house'],
            'TO_HOUSE' => $inParams['to_house'],
            'FROM_HOUSING' => $inParams['from_housing'],
            'TO_HOUSING' => $inParams['to_housing'],
            'FROM_PORCH' => $inParams['from_porch'],
            'TO_PORCH' => $inParams['to_porch'],
            'FIO' => $inParams['fio'],
            'TEL' => preg_replace('~\D~', '', $inParams['phone']) ? preg_replace('~\D~', '', $inParams['phone']) : '---',
            'PHONE' => preg_replace('~\D~', '', $inParams['phone']) ? preg_replace('~\D~', '', $inParams['phone']) : '---',
            'TIP' => $inParams['type_avto'],
            'TYPE_AUTO' => $inParams['type_avto'],
            'COMM' => $inParams['comment'],
            'COMMENT' => $inParams['comment'],
            'DOP' => $dop,
            'CITY_OTKUDA' => $inParams['fromCity'],
            'OTKUDA' => $inParams['from_street'] ? $inParams['from_street'] : '---',
            'CITY_KUDA' => $inParams['toCity'],
            'KUDA' => $inParams['toStreet'] . ' '. $inParams['to_house'],
        );

        $res = CIBlock::GetList(
                        Array(), Array(
                    'SITE_ID' => SITE_ID,
                    'ACTIVE' => 'Y',
                    "CNT_ACTIVE" => "Y",
                    "CODE" => 'orders',
                        ), true
        );
        $iBlockId = false;
        $ar_res = null;
        while ($ar_res = $res->Fetch()) {
            $iBlockId = $ar_res['ID'];
        }

        $element_id = null;

        if ($iBlockId) {
            $element_id = $order->Add(array(
                'NAME' => 'новый заказ через API_INTEGRATION',
                'ACTIVE' => 'Y',
                'ACTIVE_FROM' => date("d.m.Y H:i:s"),
                'IBLOCK_ID' => $iBlockId,
                'IBLOCK_CODE' => 'orders',
                'PROPERTY_VALUES' => $bitrixProperties
            ));
        }

        $db_user = CUser::GetList(($by = 'email'), ($order = 'asc'), array('ID' => 1), array('FIELDS' => array('EMAIL')));
        if ($user = $db_user->Fetch()) {
            $arEventFields = array(
                'EMAIL' => $user['EMAIL'],
                'DEFAULT_EMAIL_FROM' => $user['EMAIL']
            );

            $adminEmail = COption::GetOptionString('main', 'email_from', 'default@admin.email');

            $arEventFields['EMAIL'] = $adminEmail;
            $arEventFields['DEFAULT_EMAIL_FROM'] = $adminEmail;
            $arEventFields['SITE_NAME'] = $site['SITE_NAME'];
            $arEventFields['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
            $arEventFields = array_merge($arEventFields, $bitrixProperties);
            CEvent::SendImmediate("NEW_ORDER_TAXI", SITE_ID, $arEventFields);
        }

        echo $element_id;
    }
}
shit_sduwe38328dsj();
ob_flush();
exit();