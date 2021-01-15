<?php

/**
 * Валидатор адаптера
 */
class GootaxAdapterValidator extends TaxiValidator
{

    const  IP        = 'ca2.gootax.pro';
    const  PORT      = '8089';
    const  KEY       = '12ce23vr34r43v243rbvv2t4fd';
    const  APPID     = "13703";
    const  TENANT_ID = "8936";
    


    /**
     * Проверка промокода
     * @param string $promocode
     */
    public function validate_createOrder($params)
    {
        if ($params['promocode']) {
            $sendPhone = preg_replace('/\D/', '', $params['phone']);

            $optionsProfile= ['phone' => $sendPhone, 'current_time' => time()];

            $resultProfile = $this->send('get','api-site/get_client_profile', $optionsProfile);
            if($resultProfile && !$resultProfile['result']){
                return true;
            }

            $options_promo = [
                'phone'        => $sendPhone,
                'current_time' => time(),
                'code'         => $params['promocode'],
            ];

            if (!isset($_COOKIE['promo'])) {
                $resultPromo = $this->send('post','api-site/activate_referral_system_code', $options_promo);
                if ($resultPromo  && $resultPromo['code']!=0) {
                    $this->addError('promocode', 'Невалидный промокод');
                } else {
                    setcookie('promo', $params['promocode'], time()+600, '/'); 
                }
            } else {
                if ($params['promocode'] != $_COOKIE['promo'] ) {
                    $resultPromo = $this->send('post','api-site/activate_referral_system_code', $options_promo);
                    if ($resultPromo  && $resultPromo['code']!=0) {
                        $this->addError('promocode', 'Невалидный промокод');
                    } else {
                        setcookie('promo', $params['promocode'], time()+600, '/'); 
                    }
                }
            }
        }
    }

    public function send($type,$method, $params = '')
    {
        $lang = 'ru';;
        if (is_array($params)) {
            $paramsString = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        }

        if ($type === 'post'){
            $url = $this->getUrl($method);
        } else {
            $url = $this->getUrl($method)  . "?" . $paramsString;
        }

        $signature = $this->getSignature($paramsString);
        $headers = [
            'typeclient: web',
            "lang: {$lang}",
            "tenantid: ". GootaxAdapterValidator::TENANT_ID,
            "appid: " . GootaxAdapterValidator::APPID,
            "signature: {$signature}",
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, $type === 'post' ? true:false);
        if($type === 'post'){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $errorCode = curl_errno($ch);
        curl_close($ch);
        return ($errorCode == CURLE_OK) ? json_decode($result,true) : false;
    }

    private function getUrl($method)
    {
        return "https://". GootaxAdapterValidator::IP . ":" . GootaxAdapterValidator::PORT ."/{$method}";
    }

    private function getSignature($params)
    {
        return MD5($params . GootaxAdapterValidator::KEY);
    }


}
