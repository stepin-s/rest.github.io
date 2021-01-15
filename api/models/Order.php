<?php
include_once __DIR__ . '/_Base.php';
include_once __DIR__ . '/AcceptPassword.php';

class Order extends Base
{
    public $addressA;
    public $addressB;
    public $tariffId;
    public $passenger;
    public $children;
    public $name;
    public $time;
    public $phone;
    public $comment;
    public $numberflight;

    public $password;

    protected $points;
    protected $method = 'create_order_site';

    public function __construct()
    {
        parent::__construct();
        $this->points = $GLOBALS['params']['points'] ?: [];
    }

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'addressA' => self::REQ,
                'addressB' => self::SAFE,
                'tariffId' => self::REQ,
                'passenger' => self::REQ,
                'children' => self::SAFE,
                'name' => self::REQ,
                'time' => self::REQ,
                'phone' => self::REQ,
                'password' => self::REQ,
                'comment' => self::SAFE,
                'numberflight' => self::SAFE,
            ]
        );
    }

    public function getParams()
    {
        return array_merge(
            parent::getParams(),
            [
                'address' => $this->getAddress(),
                'city_id' => $GLOBALS['params']['cityId'],
                'client_phone' => $this->phone,
                'tariff_id' => $this->tariffId,
                'type_request' => 1,
                'comment' => $this->getComment(),
                'order_time' => $this->getTime(),
                'pay_type' => 'CASH',
            ]
        );
    }

    public function getTime()
    {
        if (!preg_match('/^(\d{1,2}\.){2}\d{4}\s\d{2}\:\d{2}$/', $this->time)) {
            $this->_errors['time'] = 'Incorrected param time';
        }
        return str_replace('в ', '', $this->time);
    }

    public function getAddress()
    {
        $address['address'][] = $this->addressA;
        if (!empty($this->addressB)) {
            $address['address'][] = $this->addressB;
        }
        return json_encode($address, JSON_UNESCAPED_UNICODE);
    }

    public function getComment()
    {
        $comment = [];

        $comment[] = 'Кол-во пассажиров: ' . $this->passenger;
        if (!empty($this->children)) {
            $comment[] = 'Кол-во детей: ' . $this->children;
        }
        if (!empty($this->numberflight)) {
            $comment[] = 'Номер рейса: ' . $this->numberflight;
        }
        if (!empty($this->comment)) {
            $comment[] = $this->comment;
        }
        
        return implode(PHP_EOL, $comment);
    }



    public function setAddressByPoint($pointId)
    {
        if (!empty($this->points[$pointId])) {
            $this->addressA = $this->points[$pointId];
        } else {
            $this->_errors['points'] = 'Empty data is point = ' . $pointId;
        }
    }

    /**
     * Получение списка аэропортов
     * @return array
     */
    public function getPoints()
    {
        $list = [];

        foreach ($this->points as $key => $value) {

            $list[] = [
                'id' => $key,
                'label' => $value['label'],
            ];
        }
        return $list;
    }


    public function isCreated()
    {
        $response = $this->getResponse();
        return !empty($response['result']['order_id']);
    }

    public function getOrderId()
    {
        $response = $this->getResponse();

        return $response['result']['order_id'] ?: null;
    }

    public function getOrderNumber()
    {
        $response = $this->getResponse();
        return $response['result']['order_number'] ?: null;
    }

    public function send($type = 'get') {
        if (!$this->validate()) {
            return false;
        }
        $model = new AcceptPassword();
        $model->phone = $this->phone;
        $model->password = $this->password;
        $model->post();
        if (!$model->isAccept()) {
            $this->_errors['pasword'] = 'Incorrect password';
            return false;
        }
        return parent::send($type);
    }
}