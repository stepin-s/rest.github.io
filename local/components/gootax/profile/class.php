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

class Profile extends CBitrixComponent implements Controllerable
{
    private $config = [];

    protected const REQUIRED_FIELDS = [
        'name' => [
            'type' => 'string',
            'error' => 'Укажите Ваше имя',
        ],
        'surname' => [
            'type' => 'string',
            'error' => 'Укажите Вашу фамилию',
        ],
    ];

    private function getAdresses() {
        $addresses = new GProfile();
        $userInfo = GGootaxbase::getUserInfo();

        $addresses = $addresses->getAddresses($userInfo['LOGIN']);
        $this->arResult['userinfo'] = GGootaxbase::getUserInfo();

        foreach ($addresses as $address) {
            $this->arResult['addresses'][$address['id']] = $address;
        }

        return $this->arResult['addresses'];
    }

    public function executeComponent()
    {
        $addresses = $this->getAdresses();
//        echo '<pre>';
//        var_dump($addresses);
//        echo '</pre>';
        $this->includeComponentTemplate();
    }

    public function saveProfileAction($data): AjaxJson {
        $profile = new GProfile();
        $res = $profile->updateClientProfile($data);

        return AjaxJson::createSuccess($res);
    }

    public function newAddressAction($data): AjaxJson {
        $addresses = new GProfile();

        $res = $addresses->newAddress($data);

        return AjaxJson::createSuccess($res);
    }

    public function updateAddressAction($data): AjaxJson {
        $addresses = new GProfile();

        $res = $addresses->newAddress($data);

        return AjaxJson::createSuccess($res);
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
        ];
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
            $validateFields['another_name'] = [
                'type' => 'string',
                'error' => 'Укажите имя получателя',
            ];
            $validateFields['another_surname'] = [
                'type' => 'string',
                'error' => 'Укажите фамилию получателя',
            ];
            unset($validateFields['name']);
            unset($validateFields['surname']);
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


    public function getHtmlAction($data, $template): AjaxJson
    {
        if (empty($template)) {
            $error = new Error('Не выбран шаблон');
            $errorCollection = new ErrorCollection([$error]);

            return AjaxJson::createError($errorCollection);
        }


        try {
            if ($template == 'edit') {
                $template = 'modalEdit';
                $this->arResult['data'] = $data;
                $allAddresses = $this->getAdresses();
                $this->arResult['addresses'] = $allAddresses[$data['id']];
            }
            if ($template == 'add') {
                $template = 'modalAdd';
            }
        } catch (Exception $e) {
            $error = new Error($e);
            $errorCollection = new ErrorCollection([$error]);

            return AjaxJson::createError($errorCollection);
        }

        return AjaxJson::createSuccess([
            'html' => $this->getUpdateTemplate($template),
            'step' => $template,
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
