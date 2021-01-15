<?php

include_once __DIR__ . '/_Base.php';

class SendPassword extends Base
{

    public $phone;

    public function __construct()
    {
        parent::__construct();

        $this->method = 'send_password';
    }

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'phone' => self::REQ,

            ]
        );
    }

    public function getParams()
    {
        return array_merge(
            parent::getParams(),
            [
                'phone'   => $this->phone,
            ]
        );
    }

    public function isSend()
    {
        $response = $this->_response;

        return !empty($response['result']['password_result']);
    }

}