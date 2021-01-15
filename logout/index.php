<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>

<?

use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

global $USER;

if ($USER->IsAuthorized()) {
    $cookieField = ['api_browser_key', 'api_token', 'phone', 'items'];
    foreach ($cookieField as $field) {
        $cookie = new Cookie($field, '');
        //$cookie->setDomain("example.com");
        Application::getInstance()->getContext()->getResponse()->addCookie($cookie);
    }

    $USER->Logout();

}?>

<script>
localStorage.setItem('items', '');
localStorage.removeItem("phone");
document.location.href = '/';
</script>


