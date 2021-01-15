<?php

use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Diag;

class GProfile
{

    private function currentTimeStamp() {
        $timestamp = new \Bitrix\Main\Type\DateTime();

        return $timestamp->getTimestamp();
    }

    public function getProfile($phone)
    {
        $gootaxbase = new GGootaxbase();

        $res = $gootaxbase->send('get', 'v1/get_client_profile', 'current_time='.$this->currentTimeStamp().'&phone='.$phone, '2.1.19');

        $resArr = json_decode($res, true);

        return (count($resArr['result']) > 0) ? $resArr['result'] : false;
    }

    public function getAddresses($phone = false)
    {
        $gootaxbase = new GGootaxbase();

        if($phone == false) {
            $phone = $gootaxbase->getUserInfo();
            $phone = $phone['LOGIN'];
        }

        $res = $gootaxbase->send('get', 'v1/get_client_own_addresses', 'current_time='.$this->currentTimeStamp().'&phone='.$phone, '2.1.19');

        $resArr = json_decode($res, true);

        return (count($resArr['result']) > 0) ? $resArr['result'] : [];
    }

    /**
     * Добавление нового адреса
     * @param array $address
     * @return mixed
     */
    public function newAddress2(array $address)
    {
        echo '55';
        $gootaxbase = new GGootaxbase();

        $params = [
            'current_time' => $this->currentTimeStamp(),
            'phone' => '79084200000',
            'type' => 'other',
            'street' => 'Удмуртская улица 304',
            'lat' => '56.85867691',
            'lon' => '53.22284698',
            'city' => 'Ижевск',
            'house' => '304',
            'apt' => '148',
            'comment' => 'Мой дом4'
        ];

        $res = $gootaxbase->send('post', 'v1/create_client_own_address', $params);

        $resArr = json_decode($res, true);

        return $resArr;
    }

    public function newAddress($data) {
        $gootaxbase = new GGootaxbase();

        $clientInfo = $gootaxbase->getUserInfo();

        $timestamp = new \Bitrix\Main\Type\DateTime();

        $label = isset($data['label']) ? $data['label'] : $data['street'].' '.$data['house'].' '.$data['flat'];

        $params = [
            'current_time' => $timestamp->getTimestamp(),
            'phone' => $clientInfo['LOGIN'],
            'type' => 'other',
            'street' => $data['street'],
            'lat' => $data['lat'],
            'lon' => $data['lon'],
            'city' => 'Ижевск',
            'house' => $data['house'],
            'apt' => $data['apt'],
            'comment' => $data['comment'],
            'porch' => $data['porch']
        ];

        Diag\Debug::writeToFile($params, $varName = "", $fileName = "");

        $method = 'create_client_own_address';
        if(isset($data['id'])) {
            $params['id'] = $data['id'];
            $method = 'update_client_own_address';
        }

        $res = $gootaxbase->send('post', 'v1/'.$method, $params);

        $result = ['data' => $data, 'info' => json_decode($res, true)];

        return $result;
    }
    public function updateAddress($data) {
        $gootaxbase = new GGootaxbase();

        $clientInfo = $gootaxbase->getUserInfo();

        $timestamp = new \Bitrix\Main\Type\DateTime();

        $label = isset($data['label']) ? $data['label'] : $data['street'].' '.$data['house'].' '.$data['flat'];

        $params = [
            'id' => $data['id'],
            'current_time' => $timestamp->getTimestamp(),
            'phone' => $clientInfo['LOGIN'],
            'type' => 'other',
            'street' => $data['street'],
            'lat' => $data['lat'],
            'lon' => $data['lon'],
            'city' => 'Ижевск',
            'house' => $data['house'],
            'apt' => $data['flat'],
            'comment' => $data['comment']
        ];

        $res = $gootaxbase->send('post', 'v1/update_client_own_address', $params);

        $result = ['data' => $data, 'info' => json_decode($res, true)];

        return $result;
    }

    public function updateClientProfile($data) {
        $gootaxbase = new GGootaxbase();
        $clientInfo = $gootaxbase->getUserInfo();
        $timestamp = new \Bitrix\Main\Type\DateTime();

        $params = [
            'id' => $data['id'],
            'current_time' => $timestamp->getTimestamp(),
            'client_id' => $clientInfo['UF_CLIENT_ID'],
            'is_agent' => '0',
            'email' => $data['email'],
            'name' => $data['name'],
            'surname' => $data['surname'],
            'old_phone' => $clientInfo['LOGIN'],
            'new_phone' => $clientInfo['LOGIN'],
        ];

        $res = $gootaxbase->send('post', 'v1/update_client_profile', $params);

        $resultData = json_decode($res, true);

        if($resultData['code'] == 0)
            $this->updateProfile($data,$clientInfo['LOGIN']);

        $result = ['data' => $data, 'info' => $resultData];

        return $result;
    }

    private function updateProfile($data,$login) {
        global $USER;
        $userId = $USER->GetID();
        $user = new CUser;

        $fields = Array(
            "NAME"              => $data['name'],
            "LAST_NAME"         => $data['surname'],
            "EMAIL"             => $data['email'],
        );

        $user->Update($userId, $fields);

        return isset($user->LAST_ERROR) ? false : true;
    }

    private function checkIfLoginFree($login) {
        $rsUser = CUser::GetByLogin($login);
        if($arUser = $rsUser->Fetch())
        {
            return false;
        } else {
            return true;
        }
    }
}