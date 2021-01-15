<?php
use Bitrix\Main\Type\DateTime;

class Addresses
{
    private const MODULE_ID = 'calendar';

    public function __construct()
    {
        $taxiConfig = new TaxiConfig();

        $this->config = current($taxiConfig->getAdaptersConfig());
    }

    public function getProfile($phone)
    {
        $timestamp = new \Bitrix\Main\Type\DateTime();
        $gootaxbase = new Gootaxbase();

        $res = $gootaxbase->send('get', 'v1/get_client_profile', 'current_time='.$timestamp->getTimestamp().'&phone='.$phone, '2.1.19');

        return json_decode($res, true);
    }

    public function getAddresses($phone)
    {
        $timestamp = new \Bitrix\Main\Type\DateTime();
        $gootaxbase = new Gootaxbase();

        $params = [
            'current_time' => '1565172202',
            'phone' => '79084200000',
            'type' => 'other',
            'street' => 'Майская',
            'lat' => '37.685',
            'lon' => '38.142',
            'city' => 'Ижевск',
            'house' => '24',
            'apt' => '148',
            'comment' => 'Мой дом'
        ];

        $res = $gootaxbase->send('get', 'v1/get_client_own_addresses', $params, '2.1.19');

        return json_decode($res, true);
    }
    public function updateAddress($data) {
        $gootaxbase = new Gootaxbase();

        $clientInfo = $gootaxbase->getUserInfo();

        $timestamp = new \Bitrix\Main\Type\DateTime();

        $label = isset($data['label']) ? $data['label'] : $data['street'].' '.$data['house'].' '.$data['flat'];

        $params = [
            'current_time' => $timestamp->getTimestamp(),
            'phone' => $clientInfo['login'],
            'type' => 'other',
            'street' => $data['street'],
            'lat' => $data['lat'],
            'lon' => $data['lon'],
            'city' => 'Ижевск',
            'house' => $data['house'],
            'flat' => $data['flat'],
            'comment' => $data['comment']
        ];

        $res = $gootaxbase->send('post', 'v1/create_client_own_address', $params);

        return json_decode($res, true);
    }
}