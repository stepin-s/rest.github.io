<?php

class CUriCity extends CUriModule
{
    /*   * ************VARS************* */

    protected static $_instance;
    protected static $_module_id = "city"; //TODO - по названию папки
    protected static $_iblock_type = "cities"; //bx events
    protected $_cookie_time = 60 * 60 * 24 * 7;
    protected $_element_code;
    protected $_ar_elements = array();
    protected $_ar_elements_list = array();
    protected $_ar_current_element = array();
    protected static $_ar_css_files = array("");
    protected static $_ar_js_files = array("main.js");

    /*   * ************VARS************* */


    /*   * ************INITIATE************* */

    private function __clone()
    {

    }

    protected function __construct()
    {
        $this->define_settings();
        if (!(bool)($this->_active = $this->_ar_settings["active"]))
            return false;

        $this->define_relations();
        $this->_request_uri = $_SERVER["REQUEST_URI"];
        $this->define_uri_position();
        $this->define_elements();
        $this->define_current_element();
    }

    protected function define_constants()
    {
        $ar_current = $this->get_current_element();
        defined("CITY_ID") || define("CITY_ID", $ar_current["id"]);
        defined("CITY_CODE") || define("CITY_CODE", $ar_current["code"]);
    }

    private function define_relations()
    {
        $ar_settings = $this->get_settings();
        if ((bool)$ar_settings["define_ip"]) {
            require_once $this->get_current_dir() . "ipgeobase" . DIRECTORY_SEPARATOR . "IPGeoBase.php";
            $this->IPGeoBase = new IPGeoBase();
        }

        $this->CLang = CUriLang::get_instance();
    }

    protected function set_cookie()
    {
        $ar_settings = $this->get_settings();
        if (!$ar_settings)
            self::set_error(__METHOD__, "Проверьте файл с настройками!");

        $ar_element = $this->get_current_element();
        if (!array_key_exists($ar_settings["cookie_var"], $_COOKIE) || ($_COOKIE['CITY_GOOTAX_ID'] != $ar_element['idgootax']) || ($ar_element["code"] != $_COOKIE[$ar_settings["cookie_var"]])) {
            //setcookie($ar_settings["cookie_var"], $ar_element["code"], time() + $this->_cookie_time, self::$_site_dir);
            setcookie('CITY_LAT', $ar_element["lat"], time() + $this->_cookie_time, '/');
            setcookie('CITY_LON', $ar_element["lon"], time() + $this->_cookie_time, '/');
            setcookie('CITY_GOOTAX_ID', $ar_element["idgootax"], time() + $this->_cookie_time, '/');
        }
    }

    /*   * ************INITIATE************* */


    /*   * ************ELEMENTS************* */

    protected function get_db_elements($iblock_id)
    {
        if (!is_numeric($iblock_id))
            self::set_error(__METHOD__, "Неверный код инфоблока!");

        $ar_iblock = self::get_iblock_info($iblock_id);
        if (!check_array($ar_iblock))
            self::set_error(__METHOD__, "Проверьте настройки инфоблоков!");

        $ar_elements = array();
        $ar_filter = array("IBLOCK_ID" => $iblock_id, "ACTIVE" => "Y");
        $ar_select = array(
            "ID",
            "CODE",
            "NAME",
            "PROPERTY_DEFAULT",
            "PROPERTY_SERVICE_NAME",
            "PROPERTY_EMAIL",
            "PROPERTY_PHONE",
            "PROPERTY_CODE_CITY",
            "PROPERTY_ABOUT",
            "PROPERTY_IDGOOTAX",
            "PROPERTY_LAT",
            "PROPERTY_LON",
            "PROPERTY_LOGOTYPE",
            "PROPERTY_LOGOTYPE",
        );
        $rs_element = CIBlockElement::GetList(array("SORT" => "ASC"), $ar_filter, false, false, $ar_select);
        while ($ob_element = $rs_element->GetNextElement()) {
            foreach ($ob_element->fields as $code => $val) {
                $code = str_replace("PROPERTY_", "", $code);
                $code = str_replace("_VALUE", "", $code);
                $code = strtolower($code);

                $new_element[$code] = $val;
            }
            $new_element["default"] = ($new_element["default"] == true);
            $ar_elements[$new_element["code"]] = $new_element;
        }

        if (check_array($ar_elements))
            return $ar_elements;

        return false;
    }

    /*   * ************ELEMENTS************* */


    /*   * ************ELEMENTS_LIST************* */

    public function get_element_code()
    {
        if ($this->_element_code)
            return $this->_element_code;

        $uri_path = get_uri_path($this->get_request_uri());
        $ar_settings = $this->get_settings();
        if (!check_array($ar_settings))
            self::set_error(__METHOD__, "Настройки не найдены!");

        if ($code = $this->find_in_uri($uri_path))
            return $code;

        elseif ($code = $this->find_in_cookie())
            return $code;

        elseif ((bool)$ar_settings["define_ip"] && ($ip = $this->define_user_ip()) && ($code = $this->define_by_ip($ip)))
            return $code;

        elseif ($code = $this->get_default_element_code())
            return $code;

        self::set_error(__METHOD__, "Не найден активный элемент!");
    }

    protected function get_default_element_code()
    {
        $ar_elements = $this->get_elements();
        if (!check_array($ar_elements))
            self::set_error(__METHOD__, "Элементы не найдены");

        foreach ($ar_elements as $code => $ar_element) {
            if ((bool)$ar_element["default"])
                return $ar_element["code"];
        }

        reset($ar_elements);
        $ar_element = current($ar_elements);

        return $ar_element["code"];
    }

    public function get_elements_list()
    {
        $ar_elements = $this->get_elements();
        if (!check_array($ar_elements))
            self::set_error(__METHOD__, "Элементы не найдены!");

        $i = 0;
        foreach ($ar_elements as $code => $ar_element)
            $ar_list[$code] = $i++;

        return $ar_list;
    }

    /*   * ************ELEMENTS_LIST************* */


    /*   * ************CACHE************* */

    protected function get_cache_file()
    {
        return $this->get_cache_dir() . $this->CLang->get_element_code() . DIRECTORY_SEPARATOR . $this->get_cache_filename();
    }

    /*   * ************CACHE************* */


    /*   * ************USER IP************* */

    private function define_user_ip()
    {
        $arHeader = array(
            'CLIENT_IP',
            'FORWARDED',
            'FORWARDED_FOR',
            'FORWARDED_FOR_IP',
            'HTTP_CLIENT_IP',
            'HTTP_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED_FOR_IP',
            'HTTP_PC_REMOTE_ADDR',
            'HTTP_PROXY_CONNECTION',
            'HTTP_VIA',
            'HTTP_X_FORWARDED',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED_FOR_IP',
            'HTTP_X_IMFORWARDS',
            'HTTP_XROXY_CONNECTION',
            'VIA',
            'X_FORWARDED',
            'X_FORWARDED_FOR',
            'REMOTE_ADDR'
        );
        $regEx = "/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/";



        foreach ($arHeader as $header) {
            if (isset($_SERVER[$header])) {

                if (stristr(',', $_SERVER[$header]) !== false) {
                    //(z.B.: X-Forwarded-For: client1, proxy1, proxy2)
                    $ip = trim(array_shift(explode(',', $_SERVER[$header])));

                    // if IPv4 address remove port if exists
                    if (preg_match($regEx, $ip) && ($pos = stripos($ip, ':')) !== false) {
                        $ip = substr($ip, 0, $pos);
                    }

                    return $ip;
                }
                else {
                    return $_SERVER[$header];
                }
            }
        }
        return false;
    }

    private function define_by_ip($ip)
    {
        $city_code = false;

        $ar_ip["orel"] = "84.201.247.242";
        $ar_ip["ekaterinburg"] = "5.43.128.1";
        $ar_ip["nizhniy-novgorod"] = "5.3.192.1";

        if (!($key = array_search($ip, $ar_ip))) {
            $info = $this->IPGeoBase->getRecord($ip);
            if (!$info)
                return false;

            //поиск города по названию на русском
            //путь к папке с кэшем
            $old_lang_code = $this->CLang->get_element_code();
            $this->CLang->set_element_code("ru");
            $ar_elements = $this->get_cache_elements();
            $this->CLang->set_element_code($old_lang_code);
            //

            foreach ($ar_elements as $code => $ar_element) {
                if (strtolower($ar_element["name"]) == strtolower($info["city"]))
                    $city_code = $code;
            }
        }
        else {
            $ar_elements = $this->get_cache_elements();
            foreach ($ar_elements as $code => $ar_element) {
                if ($code == $key)
                    $city_code = $code;
            }
        }

        return $city_code;
    }

    /*   * ************USER IP************* */

}
