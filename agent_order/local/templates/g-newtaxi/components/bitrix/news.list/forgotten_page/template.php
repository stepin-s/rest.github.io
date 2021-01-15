<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<div class="f_things">
	<?if (!count($arResult["ITEMS"])):?>
		<?echo GetMessage("DEFAULT_INFO");?>
	<?else:?>

	<div class="taxi-station-wrap">
		<div class="taxi-types">
            <div class="taxi-types-inner">

				<?foreach($arResult["ITEMS"] as $key=>$arItem):?>
					<?
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					?>

					<div class="taxi-type item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
						
							<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
								<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>

										<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" />

								<?else:?>
									<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" />
								<?endif;?>
							<?endif?>

							<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>

								<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
									<div class="title"><?echo $arItem["NAME"]?></div>
								<?else:?>
									<div class="title"><?echo $arItem["NAME"]?></div>
								<?endif;?>
							<?endif;?>

							<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
								<p><?echo $arItem["PREVIEW_TEXT"];?></p>
							<?elseif($arItem["DETAIL_TEXT"]):?>
								<p><?echo $arItem["DETAIL_TEXT"]?></p>
							<?endif;?>
						
					</div>
				<?endforeach;?>
			</div>
        </div>
	</div>

	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<?=$arResult["NAV_STRING"]?>
	<?endif;?>
<?endif;?>
</div>
