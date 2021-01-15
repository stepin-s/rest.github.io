<?php




class TaxiCommonValidator extends TaxiValidator {

    
    public function validate_createOrder($params) {
        if (empty($params['fromStreet']) && empty($params['fromHouse'])) {
            $this->addError('fromStreet', "Поле откуда должно быть заполнено");
        }

        /*if (empty($params['toStreet'])) {
            $this->addError('toStreet', "Поле куда должно быть заполнено");
        }*/
        $this->ruleRequire($params, array(
            'phone' => 'Поле телефон должно быть заполнено',
        ));
		
		if (strlen($params['phone']) < 11) {
            $this->addError('phone', "Поле телефон должно быть заполнено");
        }

    }

    public function validate_createOrder_priorTime($value)
    {
        $time = $this->parseTime($value);
        if ($time && ($time < time() + 60)) {
            $this->addError('priorTime', "Дата и время {$priorTime} меньше текущей");
        }
    }

}
