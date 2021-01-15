<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\Error;
use Bitrix\Main\Diag;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

Loc::loadMessages(__FILE__);

class Order extends CBitrixComponent implements Controllerable
{
    private $config = [];
    private $tenatAddresses = [];

    protected const REQUIRED_FIELDS = [
        'name' => [
            'type' => 'string',
            'error' => 'Укажите Ваше имя',
        ],
        'surname' => [
            'type' => 'string',
            'error' => 'Укажите Вашу фамилию',
        ],
        'street' => [
            'type' => 'string',
            'error' => 'Укажите улицу',
        ],
        /*'room' => [
            'type' => 'string',
            'error' => 'Укажите номер квартиры',
        ],*/
        'phone' => [
            'type' => 'string',
            'error' => 'Укажите телефон',
        ],
        'payment-type' => [
            'type' => 'string',
            'error' => 'Укажите тип оплаты',
        ],
        /*'house' => [
            'type' => 'string',
            'error' => 'Укажите дом',
        ],*/
        /*'floar' => [
            'type' => 'string',
            'error' => 'Укажите этаж',
        ],
        'flat' => [
            'type' => 'string',
            'error' => 'Заполните поле название бейджа',
        ],*/
        /*'entrance' => [
            'type' => 'string',
            'error' => 'Укажите подъезд',
        ],*/
        /*'delivery-type' => [
            'type' => 'string',
            'error' => 'Выберите тип доставки',
        ],*/
        /*'delivery-time' => [
            'type' => 'string',
            'error' => 'Выберите желаемое время доставки',
        ],*/
        'delivery-for' => [
            'type' => 'string',
            'error' => '?',
        ],
    ];

    private function checkMinCart() {
        $tenat = new GTenat();
        $cart = Application::getInstance()->getContext()->getRequest()->getCookie("items");

        $minTotal = $tenat->getProviderMinTariff();
        $cartTotal = $this->getRealPrices($cart);

        return [
            'need' => $cartTotal > $minTotal ? true : $minTotal - $cartTotal,
            'mincost' => $minTotal,
            'cart' => $cart
        ];
    }

    public function checkLoginAction(): AjaxJson
    {
        global $USER;

        if ($USER->IsAuthorized()) {
            return AjaxJson::createSuccess(
                true
            );
        } else {
            return AjaxJson::createSuccess(
                false
            );
        }
    }

    private function getConfig()
    {
        $taxiConfig = new TaxiConfig();

        $this->config = current($taxiConfig->getAdaptersConfig());
    }

    private function saveClientProfile($phone, $clientId, $name, $surname)
    {
        $user = new CUser;
        $gootaxbase = new GGootaxbase();

        $fields = [
            "NAME" => $name,
            "LAST_NAME" => $surname,
        ];

        $user->Update($this->getUserId(), $fields);

        $data = [
            'client_id' => $clientId,
            'name' => $name,
            'is_agent' => '0',
            'new_phone' => $phone,
            'old_phone' => $phone,
            'surname' => $surname,
        ];

        $result = $gootaxbase->send('post', 'v1/update_client_profile', $data);
    }

    public function executeComponent()
    {
        CJSCore::Init(["fx"]);

        $arJsConfig = [
            'picker' => [
                //'js' => 'https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js',
                'css' => 'https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css',
            ],
        ];

        foreach ($arJsConfig as $ext => $arExt) {
            \CJSCore::RegisterExt($ext, $arExt);
        }

        \CJSCore::Init(array_keys($arJsConfig));

        $clientAddresses = new GProfile();
        $addresses = new GTenat();

        $this->arResult['cart2'] = $this->getCart();
        $this->arResult['min_line'] = $this->checkMinCart();
        $this->arResult['client_addresses'] = $clientAddresses->getAddresses();
        $this->arResult['tenat_addresses'] = $addresses->getProviders();
        $this->tenatAddresses = $this->arResult['tenat_addresses'];//
        
        $this->includeComponentTemplate();
    }

    protected function getUserId()
    {
        global $USER;

        $userId = $USER->GetID();

        return $userId ? $userId : false;
    }

    protected function getUserInfo()
    {
        global $USER;

        return [
            'name' => $USER->GetFirstName(),
            'surname' => $USER->GetLastName(),
        ];
    }

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'send' => [
                'prefilters' => [],
            ],
            'getHtml' => [
                'prefilters' => [],
            ],
            'calculateDeliveryCost' => [
                'prefilters' => [],
            ],
            'storeCookie' => [
                'prefilters' => [],
            ],
            'getCookie' => [
                'prefilters' => [],
            ],
            'checkLogin' => [
                'prefilters' => [],
            ],
            'checkCoord' => [
                'prefilters' => [],
            ],
        ];
    }

    public function checkCoordAction($data): AjaxJson {

    }

    private function validateCreateOrder($data)
    {
        $validateFields = self::REQUIRED_FIELDS;
        $errors = [];
        if ($data['delivery-for'] == 'ordertoanother') {
            $validateFields['another_name'] = [
                'type' => 'string',
                'error' => 'Укажите имя получателя',
            ];
            $validateFields['another_phone'] = [
                'type' => 'string',
                'error' => 'Укажите телефон получателя',
            ];
            /*$validateFields['another_surname'] = [
                'type' => 'string',
                'error' => 'Укажите фамилию получателя',
            ];*/
            unset($validateFields['name']);
            unset($validateFields['surname']);
        }

        if($data['delivery-time'] == 'wish-time') {
            $validateFields['wish-time'] = [
                'type' => 'string',
                'error' => 'Выберите желаемое время доставки',
            ];
        }

        foreach ($validateFields as $key => $field) {
            if (!isset($data[$key]) || $data[$key] == '') {
                $errors[$key] = $field['error'];
            }
        }

        return $errors;
    }

    public function getCookieAction($data): AjaxJson
    {
        $cookie = Application::getInstance()->getContext()->getRequest()->getCookie($data);

        return AjaxJson::createSuccess($cookie);
    }

    public function storeCookieAction($data): AjaxJson
    {
        $context = Application::getInstance()->getContext();
        foreach ($data as $key => $item) {
            $cookie = new Cookie($key, $item, time() + 3600 * 24 * 7 * 1000);
            $cookie->setDomain($context->getServer()->getHttpHost());
            $cookie->setHttpOnly(false);
            $cookie->setSecure(false);
            $context->getResponse()->addCookie($cookie);
            $context->getResponse()->flush("");
        }

        return AjaxJson::createSuccess(['data' => $data]);
    }

    /**
     * Экшин расчета доставки
     *
     * @param $data
     * @return AjaxJson
     */
    public function calculateDeliveryCostAction($data): AjaxJson
    {
        $client = new GGootaxbase();
        $tenat = new GTenat();
        $config = $client->getConfig();
        $clientInfo = $client->getUserInfo();

        $cart = Application::getInstance()->getContext()->getRequest()->getCookie("items");


        $clientId = $this->getClientId();
        $totalPrice = $this->getRealPrices($cart);
        $dateTime = new DateTime();
        $dateTime->add("+ 70 minutes");
        $formattedTime = $dateTime->format("d.m.Y H:i:s");

//        if(intval($data['wish-time'])>0) {
//            $formattedTime = DateTime::createFromFormat("U", $data['wish-time'])->format("d.m.Y H:i:s");
//        }

        //
        $address['address'] = [
            [
                'city_id' => $config['options']['cityId'],
                'lon' => $data['lon'],
                'apt' => '',
                'housing' => '',
//                'street' => $data['street'],
//                'house' => $data['house'],
//                'porch' => $data['entrance'],
//                'lat' => $data['lat'],
                'city' => 'Москва',
            ],
            [
                'city_id' => $config['options']['cityId'],
                'lon' => $data['lon'],
                'apt' => '',
                'housing' => '',
//                'street' => $data['street'],
//                'house' => $data['house'],
//                'porch' => $data['entrance'],
//                'lat' => $data['lat'],
                'city' => 'Москва',
            ]
        ];

        $nearest = GGootaxgeo::getNearestProvider($data['lat'], $data['lon']);
        $to = array_merge($address['address'][0], $nearest['result']);
        $address['address'][0] = $to;

        //

        $dataOrder = [
            'address' => json_encode($address, JSON_UNESCAPED_UNICODE),
            'city_id' => $config['options']['cityId'],
            'client_id' => $clientId,
            'client_phone' => preg_replace('/\D/', '', $clientInfo['LOGIN']),
            'comment' => 'Order from Bitrix',
            'current_time' => '1562753631795',
            'device_token' => '0aa41c7daef7131e1286a04f1869212ad00e4a11b98e62e81b62ea3707fd2803',
            'need_route_line' => '0',
            'order_time' => $formattedTime,
            'pay_type' => 'CASH',
            'tariff_id' => GGootaxgeo::getTariffId(),
            'type_request' => '2',
            'delivery_info' => '{"cart":' . $cart . ',"cart_cost":' . $totalPrice . ',"address_id":' . $tenatAddresses . ',"delivery_type":"' . $data['deliverytype'] . '"}',
        ];

        $resData = $client->send('post', 'v1/create_order', $dataOrder);
        $resData = json_decode($resData, true);

        return AjaxJson::createSuccess([
            'cost' => $resData['result']['cost_result']['delivery_cost'],
            'providers' => $nearest,
            'avail' => $tenat->isInPolygon($data['lat'], $data['lon']),
            'min' => $this->checkMinCart()
        ]);
    }

    private function getCart()
    {
        $this->getConfig();

        $cartItems = [];
        $cartItemsIds = [];

        $jsonCart = json_decode(Application::getInstance()->getContext()->getRequest()->getCookie("items"));

        foreach ($jsonCart as $cartItem) {
            $cartItemsIds[] = $cartItem->params->id;

            $cartItems[$cartItem->params->id.$cartItem->type->id] = [
                'id' => $cartItem->params->id,
                'type' => $cartItem->type->id,
                'quantity' => $cartItem->params->quantity,
            ];

            if (isset($cartItem->dops) && is_array($cartItem->dops)) {
                foreach ($cartItem->dops as $dop) {
                    $cartItems[$cartItem->params->id.$cartItem->type->id]['dop'][$dop->id] = [
                        'id' => $dop->id,
                    ];
                }
            }
        }

        /**
         * Получаем реальные цены из бд битрикса
         */
        $bitrixItems = [];
        $realPrices = [];

        CModule::IncludeModule("iblock");

        $dbElements = CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => 13, 'PROPERTY_ITEM_ID' => $cartItemsIds],
            false,
            [],
            ['ID', 'NAME','PROPERTY_IMAGE', 'PROPERTY_ITEM_ID', 'PROPERTY_TYPES', 'PROPERTY_DOPS', 'PROPERTY_ITEM_ID', 'PROPERTY_TYPES.NAME']
        );

        while ($arElement = $dbElements->Fetch()) {

            $dops = $arElement['PROPERTY_DOPS_VALUE'];
            $types = $arElement['PROPERTY_TYPES_VALUE'];

            $realDops = [];
            $realTypes = [];

            if (is_array($dops)) {
                foreach ($dops as $dop) {
                    $dop = json_decode($dop['TEXT']);
                    $realDops[$dop->id] = ['name' => $dop->name, 'cost' => $dop->cost];
                    $realPrices[$dop->id] = $dop->cost;
                }
            }

            if (is_array($types)) {
                foreach ($types as $type) {
                    $type = json_decode($type['TEXT']);
                    $realTypes[$type->id] = ['name' => $type->name, 'cost' => $type->cost];
                    $realPrices[$type->id] = $type->cost;
                }
            }

            $bitrixItems[$arElement['PROPERTY_ITEM_ID_VALUE']] = [
                'TYPES' => $realTypes,
                'DOPS' => $realDops,
                'NAME' => $arElement['NAME'],
                'IMAGE' => $arElement['PROPERTY_IMAGE_VALUE']
            ];
        }


        $totalCost = 0;
        $formattedCart = [];

        foreach ($cartItems as $key => $item) {
            $key = explode("_",$key)[0];

            $totalCost += (float)$realPrices[$item['type']] * $item['quantity'];//(float)


           // echo $realPrices[$item['id']];
            foreach ($item['dop'] as $dop) {
                $totalCost += (float)$realPrices[$dop['id']] * $item['quantity'];
            }

            $finalDopsArray = is_array($item['dop']) ? implode(',', array_keys($item['dop'])) : "";

            $formattedCart[$item['id'].'_'.$item['type']] = [
                "product_id" => $item['id'],
                "type_id" => intval($item['type']),
                "additive_array" => $finalDopsArray,
                "quantity" => intval($item['quantity']),
                "shop_provider" => $this->config['options']['provider_id'],
            ];
        }

        //$this->arResult['cart'] = json_encode($formattedCart);

        return [
            'cost' => $totalCost,
            'cart' => $formattedCart,
            'bxcart' => $bitrixItems
        ];
    }

    private function getRealPrices($jsonCart)
    {
        $this->getConfig();

        $cartItems = [];
        $cartItemsIds = [];

        $jsonCart = json_decode($jsonCart);

        foreach ($jsonCart as $cartItem) {
            $cartItemsIds[] = $cartItem->params->id;

            $cartItems[$cartItem->params->id.$cartItem->type->id] = [
                'id' => $cartItem->params->id,
                'type' => $cartItem->type->id,
                'quantity' => $cartItem->params->quantity,
            ];

            if (isset($cartItem->dops) && is_array($cartItem->dops)) {
                foreach ($cartItem->dops as $dop) {
                    $cartItems[$cartItem->params->id.$cartItem->type->id]['dop'][$dop->id] = [
                        'id' => $dop->id,
                    ];
                }
            }
        }

        /**
         * Получаем реальные цены из бд битрикса
         */
        $bitrixItems = [];
        $realPrices = [];

        CModule::IncludeModule("iblock");

        $dbElements = CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => 13, 'PROPERTY_ITEM_ID' => $cartItemsIds],
            false,
            [],
            ['ID', 'PROPERTY_TYPES', 'PROPERTY_DOPS', 'PROPERTY_ITEM_ID']
        );

        while ($arElement = $dbElements->Fetch()) {
            $dops = $arElement['PROPERTY_DOPS_VALUE'];
            $types = $arElement['PROPERTY_TYPES_VALUE'];

            $realDops = [];
            $realTypes = [];

            if (is_array($dops)) {
                foreach ($dops as $dop) {
                    $dop = json_decode($dop['TEXT']);
                    $realDops[$dop->id] = $dop->cost;
                    $realPrices[$dop->id] = $dop->cost;
                }
            }

            if (is_array($types)) {
                foreach ($types as $type) {
                    $type = json_decode($type['TEXT']);
                    $realTypes[$type->id] = $type->cost;
                    $realPrices[$type->id] = $type->cost;
                }
            }

            $bitrixItems[$arElement['ID']] = [
                'TYPES' => $realTypes,
                'DOPS' => $realDops,
            ];
        }


        $totalCost = 0;
        $formattedCart = [];

        foreach ($cartItems as $key => $item) {

            $totalCost += (float)$realPrices[$item['type']] * $item['quantity'];//(float)

            foreach ($item['dop'] as $dop) {
                $totalCost += (float)$realPrices[$dop['id']] * $item['quantity'];
            }

            $finalDopsArray = is_array($item['dop']) ? implode(',', array_keys($item['dop'])) : "";

            $formattedCart[] = [
                "product_id" => $item['id'],
                "type_id" => intval($item['type']),
                "additive_array" => $finalDopsArray,
                "quantity" => intval($item['quantity']),
                "shop_provider" => $this->config['options']['provider_id'],
            ];
        }

        $this->arResult['cart'] = json_encode($formattedCart);

        return $totalCost;
    }

    private function getClientId()
    {
        $userInfo = GGootaxbase::getUserInfo();

        return $userInfo['UF_CLIENT_ID'];
    }

    private function createOrderApi($orderId, $data)
    {
        $totalPrice = $this->getRealPrices($data['cart']);
        $dateTime = new DateTime();
        $dateTime->add("+ 70 minutes");
        $formattedTime = $dateTime->format("d.m.Y H:i:s");

        $clientId = $this->getClientId();

        $gootaxbase = new GGootaxbase();
        $config = $gootaxbase->getConfig();

        $this->arResult['order_id'] = $orderId;

        $street = $data['street'];
        /*if(strlen($data['porch']) > 0) {
            $street .= ' п.'.$data['porch'];
        }
        if(strlen($data['flat']) > 0) {
            $street .= ' кв.'.$data['flat'];
        }*/

        $address['address'] = [
            [
                'city_id' => $config['options']['city_id'],
                'lon' => $data['lon'],
                'apt' => '',
                'housing' => '',
                'street' => $street,
                'house' => $data['house'],
                'porch' => '',//$data['porch'],
                'apt' => '',//$data['flat'],
                'lat' => $data['lat'],
                'city' => 'Москва',
            ],
            [
                'city_id' => $config['options']['city_id'],
                'lon' => $data['lon'],
                'apt' => '',
                'housing' => '',
                'street' => $street,
                'house' => $data['house'],
                'porch' => $data['porch'],
                'apt' => $data['flat'],
                'lat' => $data['lat'],
                'city' => 'Москва',
            ]
        ];

        if($data['delivery-type'] == 'pickup') {
            unset($address['address'][1]);
            //TODO: checkifpickupavail
        }
        else {
            $nearest = GGootaxgeo::getNearestProvider($data['lat'], $data['lon']);
            $to = array_merge($address['address'][0], $nearest['result']);
            $address['address'][0] = $to;
        }

        if(intval($data['wish-time'])>0) {
            $formattedTime = DateTime::createFromTimestamp($data['wish-time'])->add("1 day")->format("d.m.Y H:i:s");
        }

        $dataOrder = [
            'address' => json_encode($address, JSON_UNESCAPED_UNICODE),
            'city_id' => $config['options']['cityId'],
            'client_id' => $clientId,
            'client_phone' => preg_replace('/\D/', '', $data['phone']),
            'comment' => $data['comment'],
            'current_time' => '1562753631795',
            'device_token' => '0aa41c7daef7131e1286a04f1869212ad00e4a11b98e62e81b62ea3707fd2803',
            'need_route_line' => '0',
            'order_time' => $formattedTime,
            'pay_type' => $data['payment-type'],
            'tariff_id' => GGootaxgeo::getTariffId(),
            'type_request' => '1',
            'delivery_info' => '{"cart":' . $this->arResult['cart'] . ',"cart_cost":' . $totalPrice . ',"address_id":' . $tenatAddresses . ',"delivery_type":"' . $data['delivery-type'] . '"}',
        ];
        //Diag\Debug::writeToFile($this->arResult['cart'], $varName = "", $fileName = "cart_".$dateTime->format("dmY-H-i").".log");
        if ($data['delivery-for'] == 'ordertoanother') {
            $dataOrder['client_passenger_phone'] = preg_replace('/\D/', '', $data['another_phone']);
            $dataOrder['client_passenger_lastname'] = $data['another_surname'];
            $dataOrder['client_passenger_name'] = $data['another_name'];
        }


        $result = $gootaxbase->send('post', 'v1/create_order', $dataOrder);

        $data['total'] = $totalPrice;

        $order = json_decode($result);

        $orderId = $order->result->order_id;

        $finalArr = [
            'response' => $result,
            'data' => $dataOrder,
            'formdata' => $data,
        ];

        if(intval($orderId)==0) {
            Diag\Debug::writeToFile($finalArr, $varName = "", $fileName = "order_errors_".$dateTime->format("dmY").".log");
            throw new Exception('Заказ не создан');
        }

        return $finalArr;
    }

    public function getHtmlAction($data, $template): AjaxJson
    {
        if (empty($template)) {
            $error = new Error('Не выбран шаблон');
            $errorCollection = new ErrorCollection([$error]);

            return AjaxJson::createError($errorCollection);
        }

        $this->arResult['getClientId'] = $this->getClientId();
        $userInfo = $this->getUserInfo();


        $this->arResult['data'] = array_merge($data, $userInfo);
        $addresses = new GTenat();
        $this->arResult['tenat_addresses'] = $addresses->getProviders();

        if ($template == 'step3') {
            $errors = $this->validateCreateOrder($data);

            if (count($errors) > 0) {
                $template = 'step2';
                $this->arResult['errors'] = $errors;
                $this->arResult['data'] = array_merge($data, $userInfo);

            } else {
                \Bitrix\Main\Loader::includeModule('iblock');
                $el = new CIBlockElement;

                $dateTime = new DateTime();
                $dateTime->add("+ 70 minutes");

                $fields = [
                    'IBLOCK_ID' => 21,
                    'DETAIL_TEXT' => json_encode($data),
                    'NAME' => 'Заказ ' . $dateTime,
                    'CREATED_BY' => $this->getUserId(),
                ];

                try {
                    $orderId = $el->Add($fields);
                    $response = $this->createOrderApi($orderId, $data);
                    $this->arResult['response'] = $response;
                    $cookie = new Cookie('items', '');

                    Application::getInstance()->getContext()->getResponse()->addCookie($cookie);

                    //$resSaveProfile = $this->saveClientProfile($data['phone'], $clientId, $data['name'], $data['surname']);

                } catch (Exception $e) {
                    //$error = new Error($e);
                    //$errorCollection = new ErrorCollection([$error]);
                    $template = 'step2';
                    $this->arResult['errors'][] = 'Произошла ошибка создания заказа';
                    //return AjaxJson::createError($errorCollection);
                }
            }
        }
        $order = json_decode($this->arResult['response']['response']);
        $orderId = $order->result->order_id;
        $this->arResult['cart'] = $this->getCart();
        return AjaxJson::createSuccess([
            'html' => $this->getUpdateTemplate($template),
            'step' => $template,
            'orderid' => $orderId,

        ]);
    }

    public function getOrderStatusAction($data): AjaxJson {
        $tenat = new GTenat();

        return AjaxJson::createSuccess([
            'result' => $tenat->getOrderStatus($data)
        ]);
    }

    private function getUpdateTemplate(string $template = ''): string
    {
        ob_start();
        $this->includeComponentTemplate($template);
        $html = ob_get_clean();

        return $html ?: '';
    }
}
