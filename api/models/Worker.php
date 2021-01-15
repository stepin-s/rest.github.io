<?php

include_once __DIR__ . '/_Base.php';
require_once __DIR__ . '/../send/PHPMailer/PHPMailerAutoload.php';

class Worker extends Base
{

    const PARTNERSHIP = 'PARTNER';
    const ACTIVATE = '0';
    const POSITION_ID = 1;
    const CAR_CLASS = 1;
    const CAR_OWNER = 'WORKER';
    const PATTERN_CREATE_WORKER = 'createWorker.html';

    // Информация о водителе
    public $cityId;
    public $lastName;
    public $name;
    public $phone;
    public $email;
    public $cardNumber;
    public $language;
    public $position;

    // Информация об автомобиле
    public $carColor;
    public $carBrand;
    public $carModel;
    public $carGosNumber;
    public $carYear;
    public $carOwner;

    // Файлы
    public $photo;
    public $carPhoto;
    public $passportScan1;
    public $passportScan2;
    public $driverLicenseScan1;
    public $driverLicenseScan2;
    public $ptsScan1;
    public $ptsScan2;
    public $dogovorTsScan;
    public $osagoScan1;


    protected $method = 'create_worker_profile';

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'cityId'     => self::REQ,
                'lastName'   => self::REQ,
                'name'       => self::REQ,
                'phone'      => self::REQ,
                'email'      => self::REQ,
                'cardNumber' => self::SAFE,
                'language'   => self::REQ,
                'position'   => self::SAFE,

                'carColor'     => self::SAFE,
                'carBrand'     => self::SAFE,
                'carModel'     => self::SAFE,
                'carGosNumber' => self::SAFE,
                'carYear'      => self::SAFE,
                'carOwner'      => self::SAFE,

                'photo'              => [self::REQ, self::FILE],
                'carPhoto'           => [self::REQ, self::FILE],
                'passportScan1'      => [self::REQ, self::FILE],
                'passportScan2'      => [self::REQ, self::FILE],
                'driverLicenseScan1' => [self::REQ, self::FILE],
                'driverLicenseScan2' => [self::REQ, self::FILE],
                'ptsScan1'           => self::FILE,
                'ptsScan2'           => self::FILE,
                'osagoScan1'         => self::FILE,
                'dogovorTsScan'         => self::FILE,
            ]
        );
    }

    public function getParams()
    {
        return array_merge(
            parent::getParams(),
            [
                'city_id'     => $this->cityId,
                'last_name'   => $this->lastName,
				'second_name' => '',
				'birthday'    => '',
                'name'        => $this->name,
                'phone'       => $this->phone,
                'card_number' => $this->cardNumber,
                'email'       => $this->email,
                'lang'        => $this->language,
                'activate'    => self::ACTIVATE,
                'position_id' => self::POSITION_ID,
                'partnership' => self::PARTNERSHIP,
                'description' => $this->position,

                'car_color'  => $this->carColor,
                'car_brand'  => $this->carBrand,
                'car_model'  => $this->carModel,
                'car_year'   => $this->carYear,
                'gos_number' => $this->carGosNumber,
                'car_class'  => $this->getCarClass(),
                'car_owner'  => self::CAR_OWNER,

            ]
        );
    }

    public function getCarClass()
    {
        $type = $this->position;
        return $GLOBALS['params']['positions'][$type]['class_id'] ?: self::CAR_CLASS;
    }

    public function getFiles()
    {
        return array_merge(
            parent::getFiles(),
            [
                'photo'                  => $this->photo,
                'car_photo'              => $this->carPhoto,
                'passport_scan[0]'       => $this->passportScan1,
                'passport_scan[1]'       => $this->passportScan2,
                'osago_scan[0]'          => $this->osagoScan1,
                'driver_license_scan[0]' => $this->driverLicenseScan1,
                'driver_license_scan[1]' => $this->driverLicenseScan2,
                'pts_scan[0]'            => $this->ptsScan1,
                'pts_scan[1]'            => $this->ptsScan2,
                'dogovorTsScan'            => $this->dogovorTsScan,
            ]
        );
    }

    /**
     * Если исполнитель создался
     * @return bool
     */
    public function isCreated()
    {
        $response = $this->getResponse();

        if (empty($response)) {
            return false;
        }

        if (!empty($response['result']['worker']['errors'])) {
            $this->_errors = array_merge($this->_errors, $response['result']['worker']['errors']);

            return false;
        }

        return true;
    }

    public function getWorkerInfo()
    {
        $response = $this->getResponse();

        return $response['result']['worker'] ?: null;
    }

    public function getCallsign()
    {
        $workerInfo = $this->getWorkerInfo();

        return $workerInfo['callsign'] ?: null;
    }

    public function getPassword()
    {
        $workerInfo = $this->getWorkerInfo();

        return $workerInfo['password'] ?: null;
    }

    public function getId()
    {
        $workerInfo = $this->getWorkerInfo();

        return $workerInfo['worker_id'] ?: null;
    }


    public function getCarInfo()
    {
        $response = $this->getResponse();

        return $response['result']['car'] ?: null;
    }

    public function getCarId()
    {
        $carInfo = $this->getCarInfo();

        return $carInfo['car_id'] ?: null;
    }


    public function getPositionList()
    {
        $lang  = $this->language;
        $array = (array)($GLOBALS['params']['positions'] ?: []);

        array_walk($array, function (&$item, $key) use ($lang) {
            $item = $item['lang'][$lang] ?: $key;
        });

        return $array;
    }

    protected function getPattern()
    {
        $lang = $this->language;

        if (empty($lang)) {
            return '';
        }

        $fileUrl = __DIR__ . '/../patterns/' . $lang . '/' . self::PATTERN_CREATE_WORKER;

        if (is_file($fileUrl)) {
            return file_get_contents($fileUrl);
        }

        return '';
    }

    protected function getSubject()
    {
        $lang = $this->language;

        if (empty($lang)) {
            return '';
        }

        return $GLOBALS['params']['email']['subject'][$lang] ?: '';
    }

    public function sendMail()
    {
        $mail          = new PHPMailer;
        $mail->CharSet = 'utf-8';
        $mail->isSendmail();
        $mail->setFrom($GLOBALS['params']['email']['from'], $GLOBALS['params']['email']['from_title']);
        $mail->Subject = $this->getSubject();

        $pattern = $this->getPattern();

        $replace = [
            '::NAME::'           => $this->name,
            '::CALLSIGN::'       => (string)$this->getCallsign(),
            '::PASSWORD::'       => (string)$this->getPassword(),
//            '::TITLE::'          => $GLOBALS['params']['email']['pattern']['title'] ?: '',
//            '::PORTAL_ADDRESS::' => $GLOBALS['params']['email']['pattern']['portal_address'] ?: '',
//            '::HELP_PHONE::'     => $GLOBALS['params']['email']['pattern']['help_phone'] ?: '',
//            '::HELP_EMAIL::'     => $GLOBALS['params']['email']['pattern']['help_email'] ?: '',
        ];
        $body    = str_replace(array_keys($replace), $replace, $pattern);
        $mail->msgHTML($body);

        $mail->addAddress($this->email, $this->email);

        return $mail->send();
    }

}