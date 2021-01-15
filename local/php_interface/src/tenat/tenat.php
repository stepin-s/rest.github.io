<?php

use Bitrix\Main\Type\DateTime;

class GTenat
{

    private function currentTimeStamp()
    {
        $timestamp = new \Bitrix\Main\Type\DateTime();

        return $timestamp->getTimestamp();
    }

    public function isInPolygon($lat, $lon) {

        $polygons = $this->getTenantCityList();

        $points_polygon = count($polygons['vertices_x']) - 1;  // number vertices - zero-based array

        $i = $j = $c = 0;
        for ($i = 0, $j = $points_polygon ; $i < $points_polygon; $j = $i++) {
            if ( (($polygons['vertices_y'][$i]  >  $lat != ($polygons['vertices_y'][$j] > $lat)) &&
                ($lon < ($polygons['vertices_x'][$j] - $polygons['vertices_x'][$i]) * ($lat - $polygons['vertices_y'][$i]) / ($polygons['vertices_y'][$j] - $polygons['vertices_y'][$i]) + $polygons['vertices_x'][$i]) ) )
                $c = !$c;
        }
        return $c ? true : false;
    }

    /*
     * Получаем координаты полигонов для ограничения доставки
     */
    public function getTenantCityList() {
        $gootaxbase = new GGootaxbase();

        $config = $gootaxbase->getConfig();

        $res = $gootaxbase->send('get', 'v1/get_tenant_city_list',
            'city_id=' . $config['options']['cityId'] . '&current_time=' . $this->currentTimeStamp(),
            '2.1.19');

        $data = json_decode($res, true);

        /*
         * Находим город который нам нужен
         */
        $needCity = [];
        foreach ($data['result']['city_list'] as $city) {
            if($city['city_id'] == $config['options']['cityId']) {
                $needCity = $city['city_reseption_area'];
                break;
            }
        }

        if(count($needCity) == 0) {
            return false;
        }

        /*
         * Преобразуем координаты для работы функции
         */
        $vertices_x = [];
        $vertices_y = [];

        foreach ($needCity as $coord) {
            $vertices_x[] = $coord[0];
            $vertices_y[] = $coord[1];
        }


        return ['vertices_x' => $vertices_x, 'vertices_y' => $vertices_y];
    }

    public function getProviderInfo()
    {

        $gootaxbase = new GGootaxbase();

        $config = $gootaxbase->getConfig();

        $res = $gootaxbase->send('get', 'v1/get_tariffs_list',
            'city_id=' . $config['options']['cityId'] . '&current_time=' . $this->currentTimeStamp(),
            '2.1.19');

        $data = json_decode($res, true);

        $tarifId = 0;
        foreach ($data['result'] as $tarif) {

            if ($tarif['provider_id'] == $config['options']['provider_id'] && $tarif['for_shop'] == 1) {

                $tarifId = $tarif;
                break;
            }
        }

        return (count($tarifId) > 0) ? $tarifId : false;
    }

    /**
     * Получение списка адресов для провайдера
     * @return bool|int
     */
    public function getProviders()
    {

        $gootaxbase = new GGootaxbase();

        $config = $gootaxbase->getConfig();

        $res = $gootaxbase->send('get', 'v1/get_providers',
            'city_id=' . $config['options']['cityId'] . '&current_time=' . $this->currentTimeStamp(),
            '2.1.19');

        $data = json_decode($res, true);

        $tarifId = 0;
        foreach ($data['result']['items'] as $tarif) {

            if ($tarif['id'] == $config['options']['provider_id']) {

                $tarifId = $tarif['address'];
                break;
            }
        }

        return (count($tarifId) > 0) ? $tarifId : false;
    }

    public function getProviderMinTariff()
    {

        $gootaxbase = new GGootaxbase();

        $config = $gootaxbase->getConfig();

        $res = $gootaxbase->send('get', 'v1/get_providers',
            'city_id=' . $config['options']['cityId'] . '&current_time=' . $this->currentTimeStamp(),
            '2.1.19');

        $data = json_decode($res, true);

        $tarifId = 0;
        foreach ($data['result']['items'] as $tarif) {

            if ($tarif['id'] == $config['options']['provider_id']) {

                $tarifId = $tarif['min_order_sum'];
                break;
            }
        }

        return (count($tarifId) > 0) ? intval($tarifId) : false;
    }

    public function getOrderStatus($orderId)
    {
        $gootaxbase = new GGootaxbase();

        $res = $gootaxbase->send('get', 'v1/get_order_info',
            'need_car_photo=0&need_driver_photo=0&order_id='.$orderId.'&need_route_line=0&current_time=' . $this->currentTimeStamp(),
            false);

        $data = json_decode($res, true);

        return isset($data['result']['order_info']['status_name']) ? $data['result']['order_info']['status_name'] : false;
    }

}