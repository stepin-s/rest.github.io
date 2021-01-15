<?php

/*
 * Кастомный конфигуратор
 */

/**
 * Кастомный конфигуратор
 */
class TaxiMasterNewAdapterConfigurator_custom extends TaxiMasterNewAdapterConfigurator
{

    /**
     * Расчитать \ получить информацию о тарифах для этого заказа перед
     * его созданием
     * @param TaxiCreateOrderData $data - информация достаточная для создания заказа
     * @return TaxiMasterAdapterConfiguratorTariffsInfo - информация о тарифах
     */
    public function callCarAndTariffGroups($data)
    {
        $res = new TaxiMasterNewAdapterConfiguratorTariffsInfo();    
        switch ($data->carType) {
            case 'Онлайн':           
                $tariffId = '40';
                $crewId = '20';
                break;
            case 'Стандарт':          
                $tariffId = '6';
                $crewId = '3';
                break;        
            default:
                break;
        }

        $res->crewGroupId = $crewId;
        $res->tariffGroupId = $tariffId;

        return $res;
    }

}
