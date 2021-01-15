<?php

require_once __DIR__ . '/../_params.php';

class GeoService
{

    public $text;
    public $lang;
    public $lat;
    public $lon;
    protected $tenantId;
    protected $size;
    protected $radius;
    protected $format;
    protected $typeApp;
    protected $cityId;
    protected $hash;

    protected $host;
    protected $apiKey;

    public function __construct()
    {
        $this->lat = $GLOBALS['params']['geoservice']['lat'] ?: '';
        $this->lon = $GLOBALS['params']['geoservice']['lon'] ?: '';
        $this->size = $GLOBALS['params']['geoservice']['size'] ?: '';
        $this->radius = $GLOBALS['params']['geoservice']['radius'] ?: '';
        $this->format = $GLOBALS['params']['geoservice']['format'] ?: '';
        $this->typeApp = $GLOBALS['params']['geoservice']['typeApp'] ?: '';
        $this->cityId = $GLOBALS['params']['geoservice']['cityId'] ?: '';
        $this->tenantId = $GLOBALS['params']['tenantId'] ?: '';

        $this->host = $GLOBALS['params']['geoservice']['host'] ?: '';
        $this->apiKey = $GLOBALS['params']['geoservice']['apiKey'] ?: '';
    }

    public function getParams()
    {
        return [
            'text' => $this->text,
            'lang' => $this->lang,
            'focus.point.lat' => $this->lat,
            'focus.point.lon' => $this->lon,
            'size' => $this->size,
            'radius' => $this->radius,
            'format' => $this->format,
            'type_app' => $this->typeApp,
            'tenant_id' => $this->tenantId,
            'city_id' => $this->cityId,
        ];
    }

    /**
     * Преобразуем массив параметров в строку
     * @param array $params
     * @return string
     */
    public function getStringByParams($params)
    {
        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }


    public function getHash()
    {
        $params = $this->getParams();
        $paramsStr = $this->getStringByParams($params);

        return md5($paramsStr . $this->apiKey);
    }

    public function autocomplete($text, $lang, $lat = null, $lon = null)
    {
        $this->text = $text;
        $this->lang = $lang;
        if ($lat!==null) {
            $this->lat = $lat;
        }
        if ($lon!==null) {
            $this->lon = $lon;
        }

        if (strlen($this->text) < 2) {
            return [];
        }
        $method = 'autocomplete';

        $response = $this->send($method);

        //dd ([$this->lat, $this->lon]);
        
        if (empty($response['results'])) {
            return [];
        }

        $cityList = array_map(function ($item) {
            unset($item['address']['comment']);
            return $item['address']?: '';
        },$response['results']);

        return $cityList;
    }

    protected function getUrl($method)
    {
        return $this->host . $method;
    }

    public function send($method)
    {
        $url = $this->getUrl($method);
        $this->hash = $this->getHash();

        $params = $this->getParams();
        $params['hash'] = $this->getHash();
        $paramsToString = $this->getStringByParams($params);

        $response = file_get_contents($url . '?' . $paramsToString);

        $response = json_decode($response, true);

        return (array)$response;
    }

}

function dd ($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    exit;
}