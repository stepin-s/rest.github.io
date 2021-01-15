<?php

include_once __DIR__ . '/_Base.php';

class CarColor extends Base
{
    protected $method = 'get_car_color_list';

    public function getCarColorList()
    {
        $array = $this->getResponse();
        return !empty($array['result']['color']) ? $array['result']['color'] : [];
    }
}
