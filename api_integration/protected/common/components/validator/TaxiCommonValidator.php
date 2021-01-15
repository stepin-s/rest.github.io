<?php




class TaxiCommonValidator extends TaxiValidator {

    
    public function validate_createOrder($params) {

        if (empty($params['fromStreet'])) {
            $this->addError('fromStreet', "Должны быть заполнены улица и(или) номер дома");
        } else if ( stripos(strtolower($params['fromStreet']), 'вокзал') === false && 
                    stripos(strtolower($params['fromStreet']), 'эропорт') === false && 
                    stripos(strtolower($params['toStreet']), 'вокзал') === false && 
                    stripos(strtolower($params['toStreet']), 'эропорт') === false) {
            $this->addError('fromStreet', 'Одна из точек трансфера должна быть вокзалом или аэропортом' );
        } 

        if (strlen($params['phone']) < 10) {
            $this->addError('phone', "Номер телефона заполнен не правильно");
        }
        
        $this->ruleRequire($params, array(
            'name'      => 'Поле имя должно быть заполнено',
            'lastName'  => 'Поле фамилия должно быть заполнено',
        ));
    }

    public function validate_createOrder_priorTime($value)
    {
        $time = $this->parseTime($value);
        if ($time && ($time < time() + 60)) {
            $this->addError('priorTime', "Дата и время {$priorTime} меньше текущей");
        }
    }

}
