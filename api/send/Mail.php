<?php

require 'PHPMailer/PHPMailerAutoload.php';

/**
 * Класс оправки сообщений
*/

class Mail
{
	
    const CONST_DIR = 'answer/'; // директория с шаблонами
    
    /**
     * @var string $_pattern
     * @var array $_params
     * @var array $_errors
    */
    public $pattern;
    
    public $params;
    
    public $language = null;
    
    // public $_to = [                 // Мыло на которое отправляем
        //'partners@gootax.pro',
		// 'info@utap.info',
        //'m@gootax.pro',
        //'artem.khikmatov@mail.ru',
    // ]; 

    public $_to = [
        'subscribe' => ['info@utap.info', 'm@gootax.pro'],
        'question' => ['info@utap.info', 'm@gootax.pro'],
        'driver_form' => ['info@utap.info', 'm@gootax.pro'],
        'franchise' => ['partners@utap.info', 'm@gootax.pro'],
        'for_business' => ['info@utap.info', 'm@gootax.pro'],
        'for_partners' => ['info@utap.info', 'm@gootax.pro'],
        'city' => ['info@utap.info', 'm@gootax.pro'],
        'utapfood'     => ['info@utap.info', 'm@gootax.pro'],
    ];
    
    private $_from = 'info@utap.info'; // От кого
    
    private $_subject = [
        'subscribe' => 'Подписка на новости',
        'question' => 'Вопросы',
        'driver_form' => 'Анкета водителя',
        'franchise' => 'Заявка на франшизу',
        'for_business' => 'Business',
        'for_partners' => 'Partnership',
		'city' => 'Город',
	    'utapfood'     => 'Utapfood',
    ];
    private $_body = [
        'subscribe' => 'Имя: {{name}}<br/>Email: {{email}}',
        'question' => 'Имя: {{name}}<br/>Email: {{email}}<br/>Компания: {{company}}<br/>Телефон: {{tel}}',
        'driver_form' => '<h4>Личные данные</h4>
            Имя: {{name}}<br/>Фамилия: {{fname}}<br/>Телефон: {{tel}}<br/>Email: {{email}}<br/> Город: {{city}}<br/> Откуда вы узнали о нас: {{source}}<hr/>
            <h4>Данные автомобиля</h4>
            Марка: {{car_brand}}<br/> Модель: {{car_model}}<br/>Гос.номер: {{car_num}}<br/>Лицензия: {{car_lic}}<br/>Год выпуска: {{car_year}}<br/><br/>
            Цвет: {{car_color}}<br/>Класс: {{car_class}}<br/>Тип авто: {{car_type}}<br/>Опции: {{car_options}}<hr/>
            Комментарий: {{comment}}',
        'franchise' => 'Имя: {{name}}<br/>Фамилия: {{fname}}<br/>Телефон: {{tel}}<br/>Email: {{email}}<br/> Страна: {{country}}<br/>Город: {{city}}<br/>Получать новостную рассылку?: {{news}}',
        'for_business' => 'Имя: {{name}}<br/>Email: {{email}}<br/>Компания: {{company}}<br/>Телефон: {{tel}}',
        'for_partners' => '<h2>{{partnership_type}}</h2>Имя: {{name}}<br/>Email: {{email}}<br/>Компания: {{company}}<br/>Телефон: {{tel}}',
		'city' => 'Имя: {{name}}<br/>Email: {{email}}<br/> Страна: {{country}}<br/> Город: {{city}}',
        'utapfood' => 'Имя: {{name}}<br/>Email: {{email}}<br/>Компания: {{company}}<br/>Телефон: {{tel}}',
    ];
    
    private $_errors = [];
    
    private $_pattern = [
        'subscribe' => [
            'name' => ['type'=>'string','max'=> 50, 'min' => 1],
            'email' => ['type' => 'email'],
        ],
        'question' => [
            'name' => ['type' => 'string', 'max' => 50, 'min' => 1],
            'email' => ['type' => 'email'],
            'company' => ['type' => 'string', 'max' => 100, 'min' => 1],
            'tel' => ['type' => 'integer', 'max' => 15, 'min' => 2],
        ],
        'driver_form' => [
            'name' => ['type' => 'string', 'min' => 1, 'max' => 50],
            'fname' => ['type' => 'string', 'min' => 1, 'max' => 50],
            'email' => ['type' => 'email'],
            'tel' => ['type' => 'integer', 'max' => 15, 'min' => 2],
            'city' => ['type' => 'string', 'max' => 100, 'min' => 1],
            'source' => ['type' => 'string', 'max' => 500, 'min' => 1],
            'car_brand' => ['type' => 'string', 'max' => 50, 'min' => 1],
            'car_model' => ['type' => 'string', 'max' => 50, 'min' => 1],
            'car_year' => ['type' => 'integer', 'max' => 4, 'min' => 4],
            'car_color' => ['type' => 'in', 'range'=>["Бежевый","Черный","Голубой","Коричневый","Темный","Золотой","Зеленый","Серый","Оранжевый","Фиолетовый","Красный","Серебрянный","Белый","Желтый","Вишневый","Жемчужный","Мокрый асфальт","Металический"] ],
            'car_class' => ['type' => 'in', 'range'=>["Эконом","Комфорт","Бизнес","Минивэн","Микроавтобус","Эвакуатор"]],
            'car_type' => ['type' => 'in', 'range' => ["Легковой","Грузовой","Спец. техника"]],
            'comment' => ['type' => 'string', 'max' => 500, 'min' => 1],
        ],
        'franchise' => [
            'name' => ['type' => 'string', 'min' => 1, 'max' => 50],
            'fname' => ['type' => 'string', 'min' => 1, 'max' => 50],
            'city' => ['type' => 'string', 'max' => 100, 'min' => 1],
            'email' => ['type' => 'email'],
            'country' => ['type' => 'string', 'max' => 100, 'min' => 1],
        ],
        'for_business' => [
            'name' => ['type' => 'string', 'max' => 50, 'min' => 1],
            'email' => ['type' => 'email'],
            'company' => ['type' => 'string', 'max' => 100, 'min' => 1],
            'tel' => ['type' => 'integer', 'max' => 15, 'min' => 2],
        ],
        'for_partners' => [
            'name' => ['type' => 'string', 'max' => 50, 'min' => 1],
            'email' => ['type' => 'email'],
            'company' => ['type' => 'string', 'max' => 100, 'min' => 1],
            'tel' => ['type' => 'integer', 'max' => 15, 'min' => 2],
        ],
		'city' => [
			'name' => ['type' => 'string', 'max' => 50, 'min' => 1],
    	    'email' => ['type' => 'email'],
			'country' => ['type' => 'string', 'max' => 100, 'min' => 1],
            'city' => ['type' => 'string', 'max' => 100, 'min' => 1],
		],
        'utapfood' => [
            'name' => ['type' => 'string', 'max' => 50, 'min' => 1],
            'email' => ['type' => 'email'],
            'company' => ['type' => 'string', 'max' => 100, 'min' => 1],
            'tel' => ['type' => 'integer', 'max' => 15, 'min' => 2],
        ],
    ];
    
    public function setPost()
    {
        $post = array_map(function ($item){
            return htmlspecialchars($item);
        },$_POST);
        $this->pattern = $post['pattern'];
        
        unset($post['pattern']);
        
        $this->params = $post;
        
    }
    
    /**
     * Определение языка
     * @param $lang
    **/
    public function setLanguage($lang)
    {
        $this->language = $lang;
	}
	
	/**
	 * Вернуть язык
	 * @return string|null
	**/
	public function getLanguage()
	{
		return $this->language;
	}
	
    /**
	 * Вернуть список полей формы
	 * @return array
	**/
    public function getParamsList()
    {
        $result = [];
        foreach ($this->params as $key => $item){
            $result[] = $key;
        }
        
        return $result;
    }

    /**
     * Метод возвращает список всех доступных шаблонов
     * @return array
    */
    public function getPatternList()
    {
        $result = [];
        
        foreach ($this->_pattern as $key => $item){
            $result[] = $key;
        }
        
        return $result;
    }
    
    /**
     * Метод проверят шаблон среди доступных
     * @return bool
    */
    public function issetPattern()
    {
        if(empty($this->pattern)){
            $this->addError('_global','Пустой шаблон 8');
            return false;
        }
        return in_array($this->pattern, $this->getPatternList());
    }
    
    
    /**
     * Метод возвращает true при наличии ошибок
     * @return bool
    */
    public function hasErrors($key = null)
    {
        return is_null($key) ? empty($this->_errors) : empty($this->_errors[$key]) ;
    }
    
    
    /**
     * вывести сообщения об ошибке
     * @param string $key имя поля. Если не задано, то выыводятся все поля
     * @return array
    */
    public function getErrors($key = null){
        return is_null($key) ? $this->_errors : $this->_errors[$key] ;
    }
    
    /**
     * добавление ошибки
     * @param string $attribute имя проверяемого поля
     * @param string $message сообщение об ошибке
    */
    public function addError($attribute,$message)
    {
        $this->_errors[$attribute][] = $message;
    }
    
    /**
     * вывод результата
     * @return array
     * 
     * array (
     *   success => 0,
     *   error => [
     *      name => 1,
     *      email => 0,
     *   ]
     *)
    */
    public function getAnswer(){
    
        $error = [];
        $success = 1;
        if(empty($this->pattern)){
            return ['success' => 0, 'error' => []];
        }
        foreach ($this->_pattern[$this->pattern] as $key => $item){
            if(isset($this->_errors[$key])){
                $error[$key] = 1;
                $success = 0;
                continue;
            }
            $error[$key] = 0;
        }
        
        return ['success' => $success, 'error' => $error];
    }
    
    /**
	 * Получить путь до шаблона для ответа
	 * @return string|null
	**/
    public function getAnswerPattern()
    {
		$pattern = self::CONST_DIR . $this->language . '/' . $this->pattern . '.html';
		if(is_file($pattern))
			return $pattern;
			
		return null;
	}
    
    /**
	 * Отправка ответа
	**/
    public function sendAnswer() 
    {
		if($pattern = $this->getAnswerPattern()) {
			$mail = new PHPMailer;
            $mail->CharSet = "utf-8";
            $mail->isSendmail();
            $mail->setFrom($this->_from, $this->_from);
            $mail->Subject = $this->_subject[$this->pattern];
            $mail->msgHTML(file_get_contents($pattern), dirname(__FILE__));
            
            $mail->addAddress($this->params['email'],$this->params['email']); 
            
            $mail->send();
		}
	}
    
    /**
     * отправка почты
     * @return boolean
    */
    public function send()
    {
        
        if($this->validate()){
            //Create a new PHPMailer instance
            $mail = new PHPMailer;
            $mail->CharSet = "utf-8";
            // Set PHPMailer to use the sendmail transport
            $mail->isSendmail();
            //Set who the message is to be sent from
            $mail->setFrom($this->_from, $this->_from);
            //Set the subject line
            $mail->Subject = $this->_subject[$this->pattern];
            //convert HTML into a basic plain-text alternative body
            $msg = preg_replace("/{{(.*?)}}/ime", "\$this->params['$1']", $this->_body[$this->pattern]);
            $mail->msgHTML($msg, dirname(__FILE__));

            if(!is_array($this->_to[$this->pattern])) {
                $mail->addAddress($this->_to[$this->pattern], $this->_to[$this->pattern]); 
            } else {
                foreach ($this->_to[$this->pattern] as $to){
                    $mail->addAddress($to,$to); 
                }
            }
            
            if($mail->send()) {
				$this->sendAnswer();
				return true;
			}
        }
        
        return false;
            
    }
   
    /**
     * валидация 
     * @return boolean
    */
    public function validate()
    {
        if($this->issetPAttern()){
            foreach($this->_pattern[$this->pattern] as $key => $item) {
                if(!in_array($key,$this->getParamsList())){
                    $this->addError($key, 'Не задано поле2');
                    continue;
                }
                switch ($item['type']){
                    case 'string':
                        $this->validateString($key, $item);
                    break;
                    case 'integer':
                        $this->validateInteger($key, $item);
                    break;
                    case 'boolean':
                        $this->validateBoolean($key);
                    break;
                    case 'email':
                        $this->validateEmail($key);
                    break;
                    case 'in':
                        $this->validateIn($key, $item);
                    break;
                    default:
                        $this->addError('_global','Неизвестное правило "' . $item['type'] . '"');
                    break;
                }
            }
        }
        else{
            $this->addError('_global','Неправильный шаблон1');
        }
        
        return $this->hasErrors();
    }
    
    /**
     * валидация строки 
     * @param $value
     * @return bool
    */
    function validateString($name, $params)
    {
        $value = $this->params[$name];
        foreach ($params as $key => $item) {
            switch ($key){
                case'max':
                    if(strlen($value) > $item){
                        $this->addError($name,'Не должно превышать ' . $item . ' символов 3');
                    }
                break;
                case'min':
                    if(strlen($value) < $item){
                        $this->addError($name,'Не должно быть менее ' . $item . ' символов 6');
                    }
                break;
            }
        }
        
        return $this->hasErrors($name);
    }
    
    /**
     * валидация числа
     * @param $value
     * @return bool
    */
    function validateInteger($name, $params)
    {
        $value = $this->params[$name];
        if($value != intval($value)){
            $this->addError($name, 'Не является целым числом 7');
        }
        else
            foreach ($params as $key => $item) {
                switch ($key){
                    case'max':
                        if(strlen($value) > $item){
                            $this->addError($name,'Не должно быть более ' . $item . ' символов 4');
                        }
                    break;
                    case'min':
                        if(strlen($value) < $item){
                            $this->addError($name,'Не должно быть менее ' . $item . ' символов 5');
                        }
                    break;
                }
            }
        
        return $this->hasErrors($name);
    }
    
    /**
     * валидация множества
     * @param $value
     * @return bool
    */
    function validateIn($name, $params)
    {
        $value = $this->params[$name];
        foreach ($params as $key => $item) {
            switch ($key){
                case'range':
                    if(!in_array($value, $params['range'])){
                        $this->addError($name,'Недопустимое значение');
                    }
                break;
            }
        }
        return $this->hasErrors($name);
    }
    
    /**
     * валидация почты
     * @return bool
    */
    function validateEmail($name)
    {
        $value = $this->params[$name];
        if(! preg_match('/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/', $value) ){
            $this->addError($name, 'Не является почтой 6');
        }

        
        return $this->hasErrors($name);
    }
    
    /**
     * валидация булевого значения
     * @return bool
    */
    function validateBoolean($name)
    {
        $value = $this->params[$name];
        if(!in_array($value,[0,1])){
            $this->addError($name, 'Не является boolean');
        }

        
        return $this->hasErrors($name);
    }
}
