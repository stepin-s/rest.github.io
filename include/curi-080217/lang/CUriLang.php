<?php

class CUriLang extends CUriModule {
  /*   * ************VARS************* */

  protected static $_instance;
  protected static $_module_id = "lang"; //TODO - по названию папки
  protected static $_iblock_type = "langs"; //bx events
  protected $_site_prefix;
  protected $_element_code;
  protected $_cookie_time = 60 * 60 * 24 * 7;
  protected $_active = false;
  protected $_ar_settings = array();
  protected $_ar_elements = array();
  protected $_ar_elements_list = array();
  protected $_ar_current_element = array();
  protected static $_ar_css_files = array("");
  protected static $_ar_js_files = array("main.js");

  /*   * ************VARS************* */


  /*   * ************INITIATE************* */

  private function __clone() {
    
  }



  protected function __construct() {
    $this->define_settings();
    if (!(bool) ($this->_active = $this->_ar_settings["active"])) {
      $this->set_element_code($this->get_default_element_code());
      return;
    }

    $this->define_elements_list();
    $this->_request_uri = $_SERVER["REQUEST_URI"];
    $this->define_uri_position();
    $this->define_element_code();
    $this->define_elements();
    $this->define_current_element();
  }

  protected function define_constants() {
    $code = $this->get_element_code();
    defined("LANGUAGE_ID") || define("LANGUAGE_ID", $code);
    defined("LANGUAGE_CODE") || define("LANGUAGE_CODE", $code);
  }
  
  protected function set_cookie()
  {
    $ar_settings = $this->get_settings();
    if (!$ar_settings)
      self::set_error(__METHOD__, "Проверьте файл с настройками!");

    $ar_element = $this->get_current_element();
    if (!array_key_exists($ar_settings["cookie_var"], $_COOKIE) || $element_code != $_COOKIE[$ar_settings["cookie_var"]]) {
      setcookie($ar_settings["cookie_var"], $ar_element["code"], time() + $this->_cookie_time, self::$_site_dir);
    }
  }

  /*   * ************INITIATE************* */


  /*   * ************ELEMENTS_LIST************* */

  public function get_elements_list($update = false) {
    if (!$update && check_array($this->_ar_elements_list))
      return $this->_ar_elements_list;

    $path = $this->get_path("cache/");
    $ar_sectios = glob($path . "*", GLOB_ONLYDIR);
    if (!check_array($ar_sectios))
      self::set_error(__METHOD__, "Элементы не найдены!");

    foreach ($ar_sectios as $i => $dir_path)
      $ar_elements[str_replace($path, "", $dir_path)] = $i;

    /*
      $ar_sectios = parent::get_elements_list();
      if (!check_array($ar_sectios))
      self::set_error(__METHOD__, "Элементы не найдены!");
     * 
     */

    return $ar_elements;
  }

  public function get_element_code() {
    if ($this->_element_code)
      return $this->_element_code;

    $uri_path = get_uri_path($this->get_request_uri());

    if ($code = $this->find_in_uri($uri_path))
      return $code;

    elseif ($code = $this->find_in_cookie())
      return $code;

    elseif ($code = $this->get_default_element_code())
      return $code;

    self::set_error(__METHOD__, "Не найден активный элемент!");
  }

  protected function get_default_element_code() {
    $ar_settings = $this->get_settings();
    
    if (array_key_exists("default_lang", $ar_settings))
      return $ar_settings["default_lang"];
    else {
      $ar_elements_list = $this->get_elements_list();
      if (!check_array($ar_elements_list))
        self::set_error(__METHOD__, "Элементы не найдены!");

      reset($ar_elements_list);

      if (!($code = array_search(current($ar_elements_list), $ar_elements_list)))
        self::set_error(__METHOD__, "Не найден элемент по умолчанию!");

      return $code;
    }
  }

  /*   * ************ELEMENTS_LIST************* */


  /*   * ************CACHE************* */

  protected function get_cache_file() {
    return $this->get_cache_dir() . $this->get_element_code() . DIRECTORY_SEPARATOR . $this->get_cache_filename();
  }

  /*   * ************CACHE************* */


  /*   * ************DIR************* */

  public function create_element($element_code) {
    if (!(string) $element_code)
      self::set_error(__METHOD__, "Отсутствует код элемента!");

    $cache_dir = $this->get_path($this->get_cache_dir());
    if (!is_dir($cache_dir . $element_code) && !mkdir($cache_dir . $element_code, 0755))
      self::set_error(__METHOD__, "Ошибка создания директории кеша!");

    $old_code = $this->get_element_code();
    $this->define_elements_list(true);

    //создать папку data.dat
    if (!$this->set_element_code($element_code))
      self::set_error (__METHOD__, "Элемент не установлен1");
    
    if (!$this->update_cache(""))
      self::set_error(__METHOD__, "Ошибка создания кэша!");

    $this->set_element_code($old_code);

    return true;
  }

  public function update_element($old_code, $new_code) {
    $cache_path = $this->get_path($this->get_cache_dir());
    if (!is_dir($cache_path))
      self::set_error(__METHOD__, "Отсутствует директория кеша!");

    if (!is_dir($cache_path . $old_code) || is_dir($cache_path . $new_code) || !rename($cache_path . $old_code, $cache_path . $new_code))
      self::set_error(__METHOD__, "Ошибка обновления директории кеша!");

    //название папки изменилось, куки удалить
    $cookie_val = $this->find_in_cookie();
    if ($cookie_val)
      $this->set_cookie($cookie_val, 0);

    return true;
  }

  /*   * ************DIR************* */


  /*   * ************DB BITRX************* */

  public function get_iblock_id($type) {
    $ar_data = parent::get_iblock_settings();
    $code = $this->get_element_code();

    if (!check_array($ar_data) || !array_key_exists($code, $ar_data))
      self::set_error(__METHOD__, "Проверьте настройки инфоблоков!");

    if (!array_key_exists($type, $ar_data[$code]))
      self::set_error(__METHOD__, "Инфоблок не найден!");

    return $ar_data[$code][$type];
  }

  /*   * ************DB BITRX************* */


  public static function tr($text, $lang = false) {

    if (!isset($lang) || !$lang)
      $lang = CUriLang::get_instance()->get_element_code();

    $slash = DIRECTORY_SEPARATOR;

    $tr_path = self::get_current_dir() . 'translates' . $slash . $lang . $slash . 'tr.ini';

    $ar_translate = parse_ini_file($tr_path);

    if (array_key_exists($text, $ar_translate)) {
      return $ar_translate[$text];
    }

    return $text;
  }
}
