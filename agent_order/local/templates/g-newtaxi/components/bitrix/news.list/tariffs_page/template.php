<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>

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
						
							<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["DETAIL_PICTURE"])):?>
								<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arResult["USER_HAVE_ACCESS"])):?>
									<a title="<?=$arItem["NAME"]?>" class="image" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
										<img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" />
									</a>
								<?else:?>
									<img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" />
								<?endif;?>
							<?endif?>

							<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>

								<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arResult["USER_HAVE_ACCESS"])):?>
									<div class="title"><a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a></div>
								<?else:?>
									<div class="title"><?echo $arItem["NAME"]?></div>
								<?endif;?>
							<?endif;?>

							<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
								<p><?echo $arItem["PREVIEW_TEXT"];?></p>
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