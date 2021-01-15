<?php

include_once __DIR__ . '/_Base.php';

class AcceptPassword extends Base
{
    public $phone;
    public $password;
    public $cityId;
    public function __construct()
    {
        parent::__construct();

        $this->cityId = $GLOBALS['params']['cityId'];
        $this->method = 'accept_password';
    }

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'phone' => self::REQ,
                'password' => self::REQ,

            ]
        );
    }

    public function getParams()
    {
        return array_merge(
            parent::getParams(),
            [
                'phone' => $this->phone,
                'password' => $this->password,
                'city_id' => $this->cityId,

            ]
        );
    }

    public function isAccept()
    {
        $response = $this->_response;
        return !empty($response['result']['accept_result']);
    }
}