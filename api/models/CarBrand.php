<?php

include_once __DIR__ . '/_Base.php';

class CarBrand extends Base
{
    protected $method = 'get_car_brand_list';

    public function getCarBrandList()
    {
        $array = $this->getResponse();
        return !empty($array['result']['brand']) ? $array['result']['brand'] : [];
    }
}
