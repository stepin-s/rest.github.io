<?
$APPLICATION->IncludeComponent("bitrix:main.feedback", "contacts", array(
    "USE_CAPTCHA" => "N",
    "OK_TEXT" => tr('form_write_us_success'),
    "EMAIL_TO" => "info@faast.taxi",
    "AJAX_OPTION_JUMP" => "N",
    "AJAX_OPTION_STYLE" => "N",
    "AJAX_OPTION_HISTORY" => "N",
    "REQUIRED_FIELDS" => array(
        0 => "NAME",
        1 => "EMAIL",
        2 => "MESSAGE",
    ),
    "EVENT_MESSAGE_ID" => array(
        0 => "7",
    )
), false
);
?>