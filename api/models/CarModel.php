<?php

include_once __DIR__ . '/_Base.php';

class CarModel extends Base
{
    public $brandId;

    protected $method = 'get_car_model_list';

    public function rules()
    {
        return array_merge(
            parent::rules(),
            ['brandId' => self::REQ]
        );
    }

    public function getParams()
    {
        return array_merge(
            parent::getParams(),
            ['brand_id' => $this->brandId]
        );
    }

    public function getCarModelList()
    {
        $array = $this->getResponse();
        return !empty($array['result']['model']) ? $array['result']['model'] : [];
    }
}
