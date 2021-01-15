<?php

abstract class CUriModule
{
    /*   * ************VARS************* */

    const URL_REWRITE = 'CURI_URL_REWRITE';

    private static $_ar_modules = array();
    private static $_ar_register_modules = array();
    public static $seo_uri;
    protected static $_site_dir = "/";
    protected static $_prefix;
    protected static $_iblock_settings = "iblock_settings.ini";
    public static $urlRewritePath = "/urlrewrite.php";

    /*   * ************VARS************* */


    /*   * ************INITIATE************* */

    public static function has_register_modules()
    {
        return (check_array(self::$_ar_register_modules));
    }

    public static function init_modules()
    {
        //в зависимости от позиции в URL
        ksort(self::$_ar_register_modules);

        foreach (self::$_ar_register_modules as $position => $module) {
            $module->init();
        }

        self::define_prefix();
    }

    protected static function define_prefix()
    {
        foreach (self::$_ar_register_modules as $uri_position => $module) {
            $ar_prefix[$uri_position] = $module->get_site_prefix();
        }

        $ajax = array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        self::$_prefix = !$ajax ? self::$_site_dir . substr(implode("/", $ar_prefix), 0, -1) : '';
        defined("SITE_PREFIX") || define("SITE_PREFIX", self::$_prefix);
    }

    public function get_site_dir()
    {
        return self::$_site_dir;
    }

    public static function get_prefix()
    {
        return self::$_prefix;
    }

    public static function register_module(CUriModule $ob_module)
    {
        $uri_position = $ob_module->get_uri_position();

        if (array_key_exists($uri_position, self::$_ar_register_modules)) {
            $ob_module->set_error(__METHOD__, "Проверьте позиции модулей в URL!");
        }

        self::$_ar_register_modules[$uri_position] = $ob_module;

        return true;
    }

    public function is_active()
    {
        $ar_settings = $this->get_settings();
        if (!$ar_settings)
            self::set_error(__METHOD__, "Проверьте настройки модуля!");

        return (bool)$ar_settings["active"];
    }

    public static function get_instance()
    {
        $class_name = get_called_class();
        if (null === $class_name::$_instance)
            $class_name::$_instance = new $class_name();

        return $class_name::$_instance;
    }

    public function init()
    {
        $this->set_cookie();

        $uri = $this->get_request_uri();
        $new_uri = $this->correct_uri($uri);

        $ajax = array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        if (($new_uri != $uri) && !$ajax)
            self::redirect($new_uri);

        $this->define_constants();
        $this->define_site_prefix();

        return true;
    }

    protected function define_site_prefix()
    {
        $prefix = $this->get_site_prefix();
        if ($prefix && !$this->set_site_prefix($prefix))
            self::set_error(__METHOD__, "Префикс не установлен!");
    }

    protected function get_site_prefix()
    {
        if ($this->_site_prefix)
            return $this->_site_prefix;

        $code = $this->get_element_code();
        $ar_settings = $this->get_settings();
        if ((bool)$ar_settings["add_in_uri"])
            return $code;
        else
            return "";
    }

    protected function set_site_prefix($prefix)
    {
        $ar_list = $this->get_elements_list();
        if (!array_key_exists($prefix, $ar_list))
            return false;

        $this->_site_prefix = $prefix;

        return true;
    }

    /*   * ************INITIATE************* */


    /*   * ************SETTINGS************* */

    protected function define_settings()
    {
        $ar_settings = $this->get_settings();
        if (!$this->set_settings($ar_settings))
            self::set_error(__METHOD__, "Проверьте настройки модуля!");

        return true;
    }

    protected function get_settings()
    {
        if ($this->_ar_settings)
            return $this->_ar_settings;

        $path = $this->get_path("settings.ini");
        $ar_data = parse_ini_file($path);
        if (!check_array($ar_data))
            return false;

        return $ar_data;
    }

    protected function check_settings($ar_settings)
    {
        if (!check_array($ar_settings))
            return false;

        if ((bool)$ar_settings["add_in_uri"] && (!is_numeric($ar_settings["uri_position"]) || ($ar_settings["uri_position"] <= 0)))
            return false;

        if (!(string)$ar_settings["cookie_var"])
            return false;

        return true;
    }

    protected function set_settings($ar_settings)
    {
        if (!$this->check_settings($ar_settings))
            return false;

        $this->_ar_settings = $ar_settings;

        return true;
    }

    /*   * ************SETTINGS************* */


    /*   * ************ELEMENTS************* */

    protected function define_elements()
    {
        $ar_elements = $this->get_elements();
        if (!$this->set_elements($ar_elements))
            self::set_error(__METHOD__, "Элементы не найдены!");

        return true;
    }

    public function get_elements($cache = true, $iblock_id = false)
    {
        if (!$cache && $iblock_id)
            return $this->get_db_elements($iblock_id);

        if (!$cache && !$iblock_id)
            self::set_error(__METHOD__, "Неверный id инфоблока!");

        /*
        При смене языка CUriLang::set_element_code(code) все равно будут браться старые элементы
        так как они уже сохранены
        */
        // if ($this->_ar_elements)
        // return $this->_ar_elements;

        return $this->get_cache_elements();
    }

    protected function get_cache_elements()
    {
        $cache_path = $this->get_cache_file();
        $path = $this->get_path($cache_path);

        $ar_elements = @unserialize(file_get_contents($path));
        if (!check_array($ar_elements))
            self::set_error(__METHOD__, "Элементы в кэше не найдены!");

        return $ar_elements;
    }

    protected function get_db_elements($iblock_id)
    {
        if (!is_numeric($iblock_id))
            self::set_error(__METHOD__, "Неверный код инфоблока!");

        $ar_iblock = self::get_iblock_info($iblock_id);
        if (!check_array($ar_iblock))
            self::set_error(__METHOD__, "Проверьте настройки инфоблоков!");

        $ar_elements = array();
        $ar_filter = array("IBLOCK_ID" => $iblock_id, "ACTIVE" => "Y");
        $ar_select = array("ID", "CODE", "NAME", "PROPERTY_DEFAULT");
        $rs_element = CIBlockElement::GetList(array("SORT" => "ASC"), $ar_filter, false, false, $ar_select);
        while ($ob_element = $rs_element->fetch()) {
            foreach ($ob_element as $code => $val) {
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

    protected function set_elements($ar_elements)
    {
        if (!check_array($ar_elements))
            return false;

        $this->_ar_elements = $ar_elements;

        return true;
    }

    /*   * ************ELEMENTS************* */


    /*   * ************CURRENT ELEMENT************* */

    protected function define_current_element()
    {
        $ar_element = $this->get_current_element();

        if (!$this->set_current_element($ar_element))
            self::set_error(__METHOD__ . get_called_class(), "Не найден активный элемент!");

        return true;
    }

    public function get_current_element()
    {
        if ($this->_ar_current_element)
            return $this->_ar_current_element;

        $code = $this->get_element_code();
        $ar_elements = $this->get_elements();

        if (array_key_exists($code, $ar_elements))
            return $ar_elements[$code];

        return false;
    }

    public function set_current_element($ar_element)
    {
        if (!check_array($ar_element))
            return false;

        if (!$this->set_element_code($ar_element["code"]))
            return false;

        $this->_ar_current_element = $ar_element;

        return true;
    }

    protected function find_in_cookie()
    {
        $ar_settings = $this->get_settings();
        if (!$ar_settings)
            self::set_error(__METHOD__, "Настройки не найдены!");

        if (array_key_exists($ar_settings["cookie_var"], $_COOKIE)) {
            $ar_elements_list = $this->get_elements_list();
            if (!check_array($ar_elements_list))
                self::set_error(__METHOD__, "Элементы не найдены!");

            if (!array_key_exists($_COOKIE[$ar_settings["cookie_var"]], $ar_elements_list))
                return false;

            return $_COOKIE[$ar_settings["cookie_var"]];
        }

        return false;
    }

    protected function find_in_uri($uri)
    {
        $uri_path = get_uri_path($uri);
        $ar_uri = explode("/", $uri_path);

        $position = $this->get_uri_position();
        if (!array_key_exists($position, $ar_uri))
            return false;

        $ar_list = $this->get_elements_list();
        if (!check_array($ar_list))
            self::set_error(__METHOD__, "Не найден список элементов!");

        if (!array_key_exists($ar_uri[$position], $ar_list))
            return false;

        return $ar_uri[$position];
    }

    /*   * ************CURRENT ELEMENT************* */


    /*   * ************ELEMENTS LIST************* */

    public function define_elements_list($update = false)
    {
        $ar_list = $this->get_elements_list($update);

        if (!$this->set_elements_list($ar_list))
            self::set_error(__METHOD__, "Ошибка списка элементов!");

        return true;
    }

    public static function get_dirs()
    {
        $ar_data = self::get_iblock_settings();
        $i = 0;
        $ar_result = array();
        foreach ($ar_data as $dir => $ar_iblocks) {
            $ar_result[$dir] = $i++;
        }

        if (!check_array($ar_result))
            self::set_error(__METHOD__, "Разделы не найдены!");

        return $ar_result;
    }

    protected function set_elements_list($ar_elements)
    {
        if (!check_array($ar_elements))
            return false;

        $this->_ar_elements_list = $ar_elements;

        return true;
    }

    protected function define_element_code()
    {
        $code = $this->get_element_code();
        if (!$this->set_element_code($code))
            self::set_error(__METHOD__, "Неверный код элемента!");

        return true;
    }

    public function set_element_code($code)
    {
        $ar_list = $this->get_elements_list();

        if (!check_array($ar_list) || !array_key_exists($code, $ar_list))
            return false;

        $this->_element_code = $code;

        return true;
    }

    /*   * ************ELEMENTS LIST************* */


    /*   * ************CACHE************* */

    protected function get_cache_dir()
    {
        $class_name = get_called_class();
        $path = "cache" . DIRECTORY_SEPARATOR;
        if (!is_dir($this->get_path($path)))
            $class_name::set_error(__METHOD__, "Отсутствует папка с кэшем!");

        return $path;
    }

    public function update_cache($data)
    {
        $cache_path = $this->get_path($this->get_cache_file(), true);
        $cache_data = @serialize($data);

        return (false !== file_put_contents($cache_path, $cache_data));
    }

    protected function get_cache_filename()
    {
        return "data.dat";
    }

    /*   * ************CACHE************* */


    /*   * ************URI POSITION************* */

    protected function define_uri_position()
    {
        $uri = $this->get_request_uri();
        $uri_path = get_uri_path($uri);
        $ar_uri = explode("/", $uri_path);

        $uri_position = $this->get_uri_position();

        if (0 && $uri_position >= count($ar_uri))
            $uri_position = $this->correct_uri_position($uri_path, $uri_position);

        if (!$this->set_uri_position($uri_position))
            self::set_error(__METHOD__, "Неверная позиция элемента в URL!");

        return true;
    }

    protected function get_uri_position()
    {
        if ($this->_uri_position)
            return $this->_uri_position;

        $ar_settings = $this->get_settings();
        if (!$ar_settings)
            self::set_error(__METHOD__, "Проверьте файл с настройками модуля!");

        return (int)$ar_settings["uri_position"];
    }

    protected function correct_uri_position($uri, $position)
    {
        $uri_path = get_uri_path($uri);
        $ar_uri = explode("/", $uri_path);

        if ("/" === substr($uri_path, -1))
            $position = count($ar_uri) - 1;
        else
            $position = count($ar_uri);

        $ar_elements = $this->get_elements_list();
        if (!check_array($ar_elements))
            self::set_error(__METHOD__, "Элементы не найдены!");

        if (array_key_exists($ar_uri[$position - 1], $ar_elements))
            return $position - 1;

        return $position;
    }

    protected function set_uri_position($uri_position)
    {
        if (!is_numeric($uri_position))
            return false;

        $this->_uri_position = $uri_position;

        return true;
    }

    /*   * ************URI POSITION************* */


    /*   * ************URI************* */

    public static function get_seo_uri()
    {
        return self::$seo_uri;
    }

    protected function get_request_uri()
    {
        return $this->_request_uri;
    }

    public static function get_real_uri($uri)
    {
        $uri_path = get_uri_path($uri);
        $ar_uri = explode("/", $uri_path);

        foreach (self::$_ar_register_modules as $uri_position => $ob_module) {
            if ($ob_module->find_in_uri($uri_path))
                unset($ar_uri[$uri_position]);
        }

        $new_uri = implode("/", $ar_uri);
        if ("" === $new_uri)
            $new_uri = self::$_site_dir;

        return $new_uri . get_uri_query($uri);
    }

    public static function redirect($new_uri)
    {
        header("Location: " . $new_uri);
        exit();
    }

    protected function correct_uri($uri)
    {
        $class_name = get_called_class();
        $new_uri = $uri_path = get_uri_path($uri);

        $ar_settings = $this->get_settings();
        if (!$ar_settings)
            $class_name::set_error(__METHOD__, "Проверьте файл с настройками!");

        $add_in_uri = $ar_settings["add_in_uri"];
        $code_in_uri = $this->find_in_uri($uri_path);
        $element_code = $this->get_element_code();

        if (!$element_code)
            $class_name::set_error(__METHOD__, "Не найден активный элемент!");

        $uri_position = $this->get_uri_position();

        if (!$code_in_uri && $add_in_uri)
            $new_uri = uri_insert($uri_path, $uri_position, $element_code);

        elseif ($code_in_uri && !$add_in_uri)
            $new_uri = uri_remove($uri_path, $uri_position);

        elseif ($code_in_uri && ($code_in_uri != $element_code)) { //change_uri(XHR)
            $new_uri = uri_remove($uri_path, $uri_position);
            $new_uri = uri_insert($new_uri, $uri_position, $element_code);
        }

        if ("" === $new_uri)
            $new_uri = $this->_site_dir;

        return $new_uri . get_uri_query($uri);
    }

    /*   * ************URI************* */


    /*   * ************DIR************* */

    protected static function get_path($postf, $create_file = false)
    { //file_put_contents создает файл
        $class_name = get_called_class();
        $path = $class_name::get_current_dir() . $postf;
        if (!is_dir($path) && (!file_exists($path) && !$create_file))
            $class_name::set_error(__METHOD__, "Проверьте правильность пути: " . $postf);

        return $path;
    }

    protected static function get_public_dir()
    {
        $class_name = get_called_class();
        $dir = $class_name::get_current_dir();
        $dir = str_replace("\\", "/", $dir);
        $dir = str_replace($_SERVER["DOCUMENT_ROOT"], "", $dir);
        return $dir;
    }

    protected static function get_current_dir()
    {
        $class_name = get_called_class();
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $class_name::$_module_id . DIRECTORY_SEPARATOR;
    }

    public static function find_file($uri)
    {
        $ar_parse_uri = parse_url($uri);
        $path = $ar_parse_uri["path"];

        if (is_dir($_SERVER["DOCUMENT_ROOT"] . $path)) {
            $index_file = self::get_index_file();
            $path .= ('/' === substr($uri, -1)) ? $index_file : ("/" . $index_file);
        }

        if (file_exists($_SERVER["DOCUMENT_ROOT"] . $path) && is_file($_SERVER["DOCUMENT_ROOT"] . $path)) {
            return $_SERVER["DOCUMENT_ROOT"] . $path;
        }

        return false;
    }

    private static function get_index_file()
    {
        return "index.php";
    }

    /*   * ************DIR************* */


    /*   * ************HTML************* */

    public static function show_elements()
    {
        $class_name = get_called_class();
        $o = $class_name::get_instance();
        if (!$o->is_active()) {
            unset($o);
            return;
        }

        $ar_elements = $o->get_elements();
        if (!check_array($ar_elements))
            return;

        $element_code = $o->get_element_code();
        $id = $class_name::get_id();

        echo "<select id='" . $id . "' data-element='" . $element_code . "' class='choose_" . $id . "'>";
        foreach ($ar_elements as $code => $ar_element) {
            echo "<option " . ($code == $element_code ? 'selected=selected' : '') . " data-element='" . $ar_element["code"] . "' value='" . $ar_element["code"] . "' id='" . $ar_element["id"] . "'>" . $ar_element["name"] . "</option>";
        }
        echo "</select>";
    }

    public static function include_css()
    {
        $class_name = get_called_class();
        if (!check_array($class_name::$_ar_css_files))
            return;

        $id = $class_name::get_id();
        $current_dir = $class_name::get_public_dir();
        foreach ($class_name::$_ar_css_files as $i => $value) {
            echo "<link rel='stylesheet' type='text/css' href='" . $current_dir . "css/" . $value . "'/>";
        }
    }

    public static function include_js()
    {
        $class_name = get_called_class();
        if (!check_array($class_name::$_ar_js_files))
            return;

        $id = $class_name::get_id();
        $current_dir = $class_name::get_public_dir();
        foreach ($class_name::$_ar_js_files as $i => $value) {
            echo "<script src='" . $current_dir . "js/" . $value . "'></script>";
        }
    }

    public static function get_id()
    {
        $class_name = get_called_class();
        return $class_name::$_module_id;
    }

    public function define_ajax_query()
    {
        if (!check_array($_POST))
            return false;

        if (!array_key_exists("new", $_POST) || !array_key_exists("path", $_POST))
            return false;

        $id = self::get_id();
        if (true != $_POST["{$id}-ajax"] || "XMLHttpRequest" != $_SERVER["HTTP_X_REQUESTED_WITH"])
            return false;

        $ar_elements = $this->get_elements();
        if (!array_key_exists($_POST["new"], $ar_elements) || !$this->set_current_element($ar_elements[$_POST["new"]]))
            return false;

        if ($new_uri = $this->correct_uri($_POST["path"])) {
            $ar_result["path"] = $new_uri;
            $ar_settings = $this->get_settings();

            $ar_result["js_cookie"]["value"] = $ar_settings["cookie_var"] . "=" . $_POST["new"];
            $ar_result["js_cookie"]["path"] = $this->get_site_dir();
            $ar_result["js_cookie"]["expire"] = (time() + $this->_cookie_time) * 1000;
            echo json_encode($ar_result);
        }
        exit();
    }

    /*   * ************HTML************* */


    /*   * ************DB BITRX************* */

    protected static function get_iblock_settings()
    {
        $ar_data = parse_ini_file(self::$_iblock_settings, 1);
        if (!check_array($ar_data))
            return false;

        return $ar_data;
    }

    //получить ключ=значение. ключ - id инфоблока, значение - код инфоблока
    //iblock_value = iblock_code || iblock_id
    public static function get_iblock_info($iblock_value)
    {
        $ar_result = array();
        $ar_data = self::get_iblock_settings();
        if (!check_array($ar_data)) {
            self::set_error(__METHOD__, "Проверьте настройки инфоблоков!");
        }

        foreach ($ar_data as $code => $ar_iblock_settings) {
            foreach ($ar_iblock_settings as $iblock_code => $iblock_id) {
                if ($iblock_value == $iblock_id) {
                    $ar_result = array("section" => $code, "id" => $iblock_id, "code" => $iblock_code);
                    break;
                } elseif ($iblock_value == $iblock_code) {
                    $ar_result[$code] = array("section" => $code, "id" => $iblock_id, "code" => $iblock_code);
                }
            }
        }

        if (check_array($ar_result))
            return $ar_result;

        return false;
    }

    public static function get_iblock_type()
    {
        $class_name = get_called_class();
        return $class_name::$_iblock_type;
    }

    /*   * ************DB BITRX************* */

    public static function set_error($section, $message)
    {
        echo $section . "\n";
        echo $message . "\n";
        exit();
    }

    public static function rewrite_rulles()
    {
        $content =
            "\n" . "\n" .
            'if (defined(\'SITE_PREFIX\')) {
  foreach ($arUrlRewrite as $i => $rule) {
    $arUrlRewrite[$i]["CONDITION"] = substr_replace($rule["CONDITION"], SITE_PREFIX, 2, 0);
  }
  define("' . self::URL_REWRITE . '", true);
}' .
            "\n";
        return file_put_contents($_SERVER["DOCUMENT_ROOT"] . self::$urlRewritePath, $content, FILE_APPEND);
    }

}
