<?
/**
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>
<?
if (!array_key_exists('api_browser_key', $_COOKIE) && $arResult['arUser']['UF_BROWSER_KEY'])
    setcookie('api_browser_key', $arResult['arUser']['UF_BROWSER_KEY'], time() + 3600*24*7, '/');
if (!array_key_exists('api_token', $_COOKIE) && $arResult['arUser']['UF_TOKEN'])
    setcookie('api_token', $arResult['arUser']['UF_TOKEN'], time() + 3600*24*7, '/');
?>
<form id="profile_form" class="row-fluid cabinet-form" method="POST" name="form1" action="<?= SITE_PREFIX.$arResult["FORM_TARGET"] ?>" enctype="multipart/form-data">
    <? ShowError($arResult["strProfileError"]); ?>
    <?
    if ($arResult['DATA_SAVED'] == 'Y')
        ShowNote(GetMessage('PROFILE_DATA_SAVED'));
    ?>
    <?= $arResult["BX_SESSION_CHECK"] ?>
    <input type="hidden" name="lang" value="<?= LANG ?>" />
    <input type="hidden" name="ID" value=<?= $arResult["ID"] ?> />

    <div class="row-cabinet clearfix">
        <div class="span3">
            <label for=""><?= GetMessage('CITY') ?></label>
        </div>
        <div class="span8">
            <input type="text" name="PERSONAL_CITY" value="<?= $arResult["arUser"]["PERSONAL_CITY"] ?>">
        </div>
    </div>
    <div class="row-cabinet clearfix">
        <div class="span3">
            <label for=""><?= GetMessage('FIO') ?></label>
        </div>
        <div class="span8">
            <input type="text" name="NAME" value="<?= $arResult["arUser"]["NAME"] ?>">
        </div>
    </div>
    <div class="row-cabinet clearfix">
        <div class="span3">
            <label for=""><?= GetMessage('USER_PHONE') ?></label>
        </div>
        <div class="span8">
            <input type="text" name="LOGIN" value="<?= $arResult["arUser"]["LOGIN"] ?>">
            <input type="hidden" name="LOGIN_OLD" value="<?= $arResult["arUser"]["LOGIN"] ?>">
        
        </div>
    </div>
    <div class="row-cabinet clearfix">
        <div class="span3">
            <label for="NEW_PASSWORD"><?= GetMessage('NEW_PASSWORD_REQ') ?></label>
        </div>
        <div class="span8">
            <input type="password" name="NEW_PASSWORD" value="" autocomplete="off" class="bx-auth-input" />
        </div>
    </div>
    <div class="row-cabinet clearfix">
        <div class="span3">
            <label for="NEW_PASSWORD_CONFIRM"><?= GetMessage('NEW_PASSWORD_CONFIRM') ?></label>
        </div>
        <div class="span8">
            <input type="password" name="NEW_PASSWORD_CONFIRM" value="" autocomplete="off" class="bx-auth-input" />
        </div>
    </div>
    <div class="row-cabinet clearfix">
        <div class="span3">
            <label for=""><?= GetMessage('EMAIL') ?></label>
        </div>
        <div class="span8">
            <input type="text" name="EMAIL" value="<?= $arResult["arUser"]['EMAIL'] ?>">
        </div>
    </div>
    <div id="clientBalanceInfo" style="display:none">
        <div class="row-cabinet clearfix">
            <div class="span3">
                <label for="">Баланс</label>
            </div>
            <div class="span8">
                <span id="balance"></span>
            </div>
        </div>
        <div class="row-cabinet clearfix">
            <div class="span3">
                <label for="">Баланс бонусов</label>
            </div>
            <div class="span8">
                <span  id="bonusBalance"></span>
            </div>
        </div>
    </div>

    <div class="span11 button-block">
        <div id="save_profile" class="button right btn call_me"><?= GetMessage('SAVE') ?></div>
        <? $need_keys = !$arResult["arUser"]['UF_TOKEN'] || !$arResult["arUser"]['UF_BROWSER_KEY'] ? 1 : 0 ?>
        <input data-index="<?= $need_keys ?>" type="hidden" name="save" value="<?= GetMessage('SAVE') ?>" />
    </div>
</form>