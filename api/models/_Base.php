<?php

require_once __DIR__ . '/../_params.php';

class Base
{

    const REQ = 'required';
    const SAFE = 'safe';
    const FILE = 'file';

    public $currentTime;

    protected $method = 'ping';
    protected $host = 'http://ca.artem.devgootax.ru/api-site/';
    protected $apiKey = '13131313';
    protected $tenantId = 68;
    protected $typeVersion = '1.1.0';

    protected $_headers = [];
    protected $_response = null;

    protected $_langs = ['ru', 'en'];
    protected $_errors = [];


    public function __construct()
    {
        $this->currentTime = time();
        $this->host        = $GLOBALS['params']['host'];
        $this->apiKey      = $GLOBALS['params']['apiKey'];
        $this->tenantId    = $GLOBALS['params']['tenantId'];
        $this->typeVersion = $GLOBALS['params']['typeVersion'];

        $this->_langs = $GLOBALS['params']['langs'] ?: ['ru', 'en'];

        $this->addHeader('tenantId', $this->tenantId);
        $this->addHeader('typeClient', 'web');
        $this->addHeader('typeVersion', $this->typeVersion);

    }

    /**
     * Правила для атрибутов класса
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Валидация атрибутов класса
     * @return bool
     */
    public function validate()
    {
        $is_valid = true;
        foreach ($this->rules() as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $value = (array)$value;

                if (empty($this->{$attribute}) && in_array(self::REQ, $value, false)) {
                    $this->_errors[$attribute] = 'Empty param ' . $attribute;
                    $is_valid                  = false;
                }

            } else {
                $this->_errors[$attribute] = 'Is not exist';
                $is_valid                  = false;
            }
        }

        return $is_valid;
    }

    /**
     * Загрузка значений для атрибутов класса
     *
     * @param array $params
     */
    public function load($params)
    {
        $rules = $this->rules();
        foreach ($params as $attribute => $value) {
            if (array_key_exists($attribute, $rules) && property_exists($this, $attribute)) {
                $this->{$attribute} = $value;
            }
        }

        $this->loadFiles();

    }

    public function loadFiles()
    {
        $rules = $this->rules();

        foreach ($rules as $attribute => $rule) {
            $file = $GLOBALS['params']['tmp'] . '/' . $this->$attribute;
            $rule = (array)$rule;
            if (in_array(self::FILE, $rule, false)) {
                if (is_file($file)) {
                    $this->$attribute = new CURLFile($file, mime_content_type($file));
                } else {
                    $this->$attribute = null;
                }
            }
        }
    }


    /**
     * Возвращает массив ошибок
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }


    /**
     * Формирует и сортирует параметры
     * @return array
     */
    public function getParams()
    {
        $params = [
            'current_time' => $this->currentTime,
        ];
        ksort($params);

        return $params;
    }

    public function getFiles()
    {
        return [];
    }

    /**
     * Генерируем сигнатуру
     *
     * @param array $params
     *
     * @return string
     */
    public function getSignature($params)
    {
        $paramsStr = $this->getStringByParams($params);

        return md5($paramsStr . $this->apiKey);
    }

    /**
     * Преобразуем массив параметров в строку
     *
     * @param array $params
     *
     * @return string
     */
    public function getStringByParams($params)
    {
        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Добавить элемент заголовка
     *
     * @param string $name
     * @param string $value
     */
    public function addHeader($name, $value = '')
    {
        $this->_headers[$name] = $value;
    }

    /**
     * Удалить элемент заголовка
     *
     * @param string $name
     *
     * @return bool
     */
    public function deleteHeader($name)
    {
        if (array_key_exists($name, $this->_headers)) {
            unset($this->_headers[$name]);

            return true;
        }

        return false;
    }

    public function setLang($lang)
    {
        if (in_array($lang, $this->_langs, false)) {
            $this->addHeader('lang', $lang);
        }
    }

    /**
     * Возвращает массив заголовков
     * @return array
     */
    public function getHeaders()
    {
        $headers = [];
        foreach ($this->_headers as $name => $value) {
            $headers[] = $name . ': ' . $value;
        }

        return $headers;
    }

    /**
     * Возвращает адрес для запроса
     * @return string
     */
    public function getUrl()
    {
        return $this->host . $this->method;
    }

    /**
     * Отправка запроса
     *
     * @param string $type
     *
     * @return bool
     */
    public function send($type = 'get')
    {
        if (!$this->validate()) {
            return false;
        }

        $url            = $this->getUrl();
        $params         = $this->getParams();
        $paramsToString = $this->getStringByParams($params);
        $signature      = $this->getSignature($params);
        $files          = $this->getFiles();
        foreach ($files as $key => $file) {
            if (empty($file)) {
                unset($files[$key]);
            }
        }
        if (!empty($files)) {
            $params = array_merge($params, $files);
        }
        $this->addHeader('signature', $signature);
        $headers = $this->getHeaders();

        $ch      = curl_init();
        switch ($type) {
            case 'post':
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case 'get':
            default:
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $paramsToString);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if ($response) {
            $this->_response = json_decode($response, true);
        }

        return (is_array($this->_response) && array_key_exists('code',
                $this->_response) && $this->_response['code'] == '0');
    }

    /**
     * Отпрвка POST-запроса
     * @return bool
     */
    public function post()
    {
        return $this->send('post');
    }

    /**
     * отпрвка GET-запроса
     * @return bool
     */
    public function get()
    {
        return $this->send('get');
    }

    /**
     * Получить результат запроса
     * @return null|array
     */
    public function getResponse()
    {
        return $this->_response;
    }

}

function dd($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    exit;
}