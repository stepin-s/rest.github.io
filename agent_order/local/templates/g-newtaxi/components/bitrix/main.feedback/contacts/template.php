<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<div class="different-information-item">
<h2>
	<span class="sprite checkers-icon"></span>
	Написать письмо
</h2>
<?$bxajaxid = CAjax::GetComponentID($component->__name, $templateName);?>
<div id="<?='ajax_form_' . $bxajaxid?>">
<span class="result_message">
<?if(!empty($arResult["ERROR_MESSAGE"]))
{
	foreach($arResult["ERROR_MESSAGE"] as $v)
		ShowError($v);
}
if(strlen($arResult["OK_MESSAGE"]) > 0){?>
	<div class="mf-ok-text"><?=$arResult["OK_MESSAGE"]?></div>
<?}?>
</span>
<form class="message-form ajax_form" action="<?= POST_FORM_ACTION_URI?>" method="POST">
	<?=bitrix_sessid_post()?>

	<div class="field">
		<input type="text" name="user_name"  class="input-style" placeholder="<?=GetMessage("MFT_NAME")?>" value="<?=$arResult["AUTHOR_NAME"]?>">
	</div>
	<div class="field">
        <input type="text" name="user_email" class="input-style" placeholder="<?=GetMessage("MFT_EMAIL")?>" value="<?=$arResult["AUTHOR_EMAIL"]?>">
    </div>
    <div class="field">
        <textarea name="MESSAGE" class="textarea-style" placeholder="<?=GetMessage("MFT_MESSAGE")?>" rows="5" cols="40"><?=$arResult["MESSAGE"]?></textarea>
    </div>
	

	<?if($arParams["USE_CAPTCHA"] == "Y"):?>
		<div class="mf-captcha">
			<label class="control-label"><?=GetMessage("MFT_CAPTCHA")?></label>
			<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
			<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA" />
			<div class="mf-text"><?=GetMessage("MFT_CAPTCHA_CODE")?><span class="mf-req">*</span></div>
			<input type="text" name="captcha_word" size="30" maxlength="50" value="" />
		</div>
	<?endif;?>

	<div class="submit-message">
        <input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>" />
		<input class="button yellow" type="submit" name="submit" value="<?=GetMessage("MFT_SUBMIT")?>" />
    </div>
	      
</form>
</div>
</div>