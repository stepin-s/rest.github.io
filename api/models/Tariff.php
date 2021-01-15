<?php

include_once __DIR__ . '/_Base.php';

class Tariff extends Base
{

    public $cityId;

    public function __construct()
    {
        parent::__construct();

        $this->method = 'get_tariffs_list';
        $this->cityId = $GLOBALS['params']['cityId'] ?: '';
    }

    public function getParams()
    {
        return array_merge(
            parent::getParams(),
            [
                'city_id' => $this->cityId,
            ]
        );
    }

    public function getTariffList()
    {
        $array = $this->getResponse();
        $tariffList = [];

        if (is_array($array['result'])) {
            foreach ($array['result'] as $tariff) {
                $key = $tariff['tariff_id'];
                $tariffList[$key] = $tariff['tariff_name'];
            }
        }
        return $tariffList;
    }
}
