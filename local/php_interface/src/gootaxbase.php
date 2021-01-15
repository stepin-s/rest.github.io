<?php
use Bitrix\Main\Error;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Application;
use Bitrix\Main\Diag;

class GGootaxbase
{

	private $config = [];

	public function __construct()
    {
        $taxiConfig = new TaxiConfig();

        $this->config = current($taxiConfig->getAdaptersConfig());
    }

    public function getConfig() {
	    return $this->config;
    }

    public static function getUserId()
    {
        global $USER;

        $userId = $USER->GetID();

        return $userId ? $userId : false;
    }

    public static function getCart() {
        $jsonCart = Application::getInstance()->getContext()->getRequest()->getCookie("items");
        $cart = json_decode($jsonCart, true);

        $result = [];

        foreach ($cart as $cartItem) {
            $result[$cartItem['params']['id']] = $cartItem['type']['id'];
        }

        return $result;
    }

    public static function getUserInfo($userId = false) {
        if(!$userId) {
          $userId = self::getUserId();
        }

        $user = CUser::GetByID($userId);
        $userData = $user->Fetch();

        return $userData;
    }

    public function send($type, $method, $params, $versionclient = false)
    {
        $httpClient = new HttpClient([
            "waitResponse" => true,
        ]);

        if (is_array($params)) {
            $paramsString = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        }
        else {
            $paramsString = $params;
        }

        $signature = $this->getSignature($paramsString);

        $httpClient->setHeader('Content-Type', 'application/x-www-form-urlencoded', true);
        $httpClient->setHeader('signature', $signature, true);
        $httpClient->setHeader('typeclient', 'android', true);
        $httpClient->setHeader('tenantid', $this->config['options']['tenantid'], true);
        $httpClient->setHeader('lang', 'ru', true);
        $httpClient->setHeader('deviceid', $this->config['options']['deviceid'], true);
        $httpClient->setHeader('appid', $this->config['options']['appid'], true);
        $httpClient->setHeader('apiKey', "123123", true);

        if($versionclient != false) {
            $httpClient->setHeader('versionclient', $versionclient, true);
        }

        $port = isset($this->config['options']['port']) ? ":" . $this->config['options']['port'] : '';
        $url = 'https://' . $this->config['options']['ip'] . $port . '/' . $method;

        if($type == 'get')
            $response = $httpClient->get($url.'?'.$params);
        else
            $response = $httpClient->post($url, $params);

        Diag\Debug::writeToFile($response, $varName = "", $fileName = "");
        return $response;
    }

    public function getSignature($params)
    {
        return MD5($params . $this->config['options']['key']);
    }
}