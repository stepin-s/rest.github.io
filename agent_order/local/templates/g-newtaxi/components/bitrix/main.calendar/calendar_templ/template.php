<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?
if ($arParams['SILENT'] == 'Y') return;

$cnt = strlen($arParams['INPUT_NAME_FINISH']) > 0 ? 2 : 1;

for ($i = 0; $i < $cnt; $i++):?>
<span style="position: relative;" onclick="BX.calendar({node:this, field:'<?=htmlspecialcharsbx(CUtil::JSEscape($arParams['INPUT_NAME'.($i == 1 ? '_FINISH' : '')]))?>', form: '<?if ($arParams['FORM_NAME'] != ''){echo htmlspecialcharsbx(CUtil::JSEscape($arParams['FORM_NAME']));}?>', bTime: <?=$arParams['SHOW_TIME'] == 'Y' ? 'true' : 'false'?>, currentTime: '<?//=(time()+date("Z")+CTimeZone::GetOffset())?>', bHideTime: <?=$arParams['HIDE_TIMEBAR'] == 'Y' ? 'true' : 'false'?>});">
<?	if ($arParams['SHOW_INPUT'] == 'Y'):
?><input type="text" id="<?=$arParams['INPUT_NAME'.($i == 1 ? '_FINISH' : '')]?>" name="<?=$arParams['INPUT_NAME'.($i == 1 ? '_FINISH' : '')]?>" value="<?=$arParams['INPUT_VALUE'.($i == 1 ? '_FINISH' : '')]?>" <?=(Array_Key_Exists("~INPUT_ADDITIONAL_ATTR", $arParams)) ? $arParams["~INPUT_ADDITIONAL_ATTR"] : ""?>/><?
	endif;
?><img style="position: absolute; right: 5px;" src="/bitrix/js/main/core/images/calendar-icon.gif" alt="<?=GetMessage('calend_title')?>" class="calendar-icon" /><?if ($cnt == 2 && $i == 0):?><span class="date-interval-hellip">&hellip;</span><?endif;?><?
?></span><?
endfor;
?>