<?php

include_once __DIR__ . '/_Base.php';

class City extends Base
{
    protected $method = 'get_tenant_city_list';

    public function getCityList()
    {
        $array = $this->getResponse();
        $cityList = [];

        if (is_array($array['result']['city_list'])) {
            foreach ($array['result']['city_list'] as $city) {
                $key = $city['city_id'];
                $cityList[$key] = $city['city_name'];
            }
        }
        return $cityList;
    }
}
