<?php
use Bitrix\Main\Error;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Application;
use Bitrix\Main\Diag;

class GGootaxgeo
{

    public static function sortMinDist($a, $b) {
        return $b['distance'] < $a['distance'] ? 1 : -1;
    }

    public static function calculateDistance($fromLat, $fromLon, $toLat, $toLon)
    {
        $client = new GGootaxbase();
        $clientConfig = $client->getConfig();
        $dateTime = new DateTime();
        $dateTime->add("+ 70 minutes");
        $formattedTime = $dateTime->format("d.m.Y H:i:s");

        $address['address'] = [
            [
                'city_id' => $clientConfig['options']['city_id'],
                'lon' => $fromLon,
                'apt' => '',
                'housing' => '',
                'street' => '',
                'house' => '13',
                'porch' => '',
                'lat' => $fromLat,
                'city' => '',
            ],
            [
                'city_id' => $clientConfig['options']['cityId'],
                'lon' => $toLon,
                'apt' => '',
                'housing' => '',
                'street' => '',
                'house' => '13',
                'porch' => '',
                'lat' => $toLat,
                'city' => '',
            ]
        ];

        $dataOrder = [
            'address' => json_encode($address),
            'city_id' => $clientConfig['options']['cityId'],
            'current_time' => $formattedTime,
            'device_token' => '0aa41c7daef7131e1286a04f1869212ad00e4a11b98e62e81b62ea3707fd2803',
            'tariff_id' => self::getTariffId(),
            'type_request' => '2'
        ];

        $resData = $client->send('post', 'v1/create_order', $dataOrder);
        $resData = json_decode($resData, true);

        return isset($resData['result']['cost_result']['summary_distance']) ? (float)$resData['result']['cost_result']['summary_distance'] : null;
    }

	public static function getNearestProvider($lat, $lon) {
		$tenat = new GTenat();
        $providersGeo = [];
        $result = [];
        $providersInfo = $tenat->getProviders();

        foreach ($providersInfo as $providerInfo) {
            $providersGeo[$providerInfo['id']] = [
                'id' => $providerInfo['id'],
                'lat' => $providerInfo['lat'],
                'lon' => $providerInfo['lon'],
                'city' => $providerInfo['city'],
                'street' => $providerInfo['street'],
                'house' => $providerInfo['house'],
                'is_working_now' => $providerInfo['is_working_now'],
                'period' => $providerInfo['period'],
                'distance' => self::calculateDistance($providerInfo['lat'], $providerInfo['lon'], $lat, $lon)
            ];
        }

        usort($providersGeo, array("GGootaxgeo", "sortMinDist"));

        foreach ($providersGeo as $key => $providerGeo) {
            //if($providerGeo['is_working_now']) {
                $result['nearest'] = $providersGeo[$key];
              //  break;
            //}
        }

        return ['result' => $result['nearest'], 'all' => $providersGeo];
	}

    public static function getTariffId($cityId = false)
    {
        $gootaxbase = new GGootaxbase();

        $config = $gootaxbase->getConfig();

        if($cityId == false) {
            $cityId = $config['options']['cityId'];
        }

        if(intval($cityId) == 0)
            return;

        $result = $gootaxbase->send('get', 'v1/get_tariffs_list', 'city_id=' . $cityId . '&current_time=1499540124',
            '2.1.19');

        $data = json_decode($result);
        $tarifId = 0;

        foreach ($data->result as $tarif) {
            if ($tarif->provider_id == $config['options']['provider_id'] && $tarif->for_shop == 1) {
                $tarifId = $tarif;
                break;
            }
        }

        return $tarifId->tariff_id;
    }
}