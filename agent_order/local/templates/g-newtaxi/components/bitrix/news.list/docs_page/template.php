<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>

<?if (count($arResult["ITEMS"]) != 0):?>	
	    <a href="<?=SITE_TEMPLATE_PATH?>/include/rss/" title="rss" target="_blank"><img alt="RSS" src="<?=SITE_TEMPLATE_PATH?>/images/feed-icon-16x16.gif" border="0" align="right" /></a>
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>

			<div class="news" id="<?=$this->GetEditAreaId($arItem['ID']);?>">

				<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
					<div class="news-title">
						<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
							<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
								<span><?echo $arItem["DISPLAY_ACTIVE_FROM"]?> | </span>
							<?endif?>
							<a class="news-list-link" href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a>
						<?else:?>
							<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
								<span><?echo $arItem["DISPLAY_ACTIVE_FROM"]?> | </span>
							<?endif?>
							<a class="news-list-link"><?echo $arItem["NAME"]?></a>
						<?endif;?>
					</div>
				<?endif;?>

				<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
						<p><?echo $arItem["PREVIEW_TEXT"];?></p>
				<?endif;?>


				<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
					<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img class="preview_picture" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" style="float:left" /></a>
					<?else:?>
						<img class="preview_picture" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" style="float:left" />
					<?endif;?>
				<?endif?>
			</div>
			<br><br>
		<?endforeach;?>
<?endif;?>	