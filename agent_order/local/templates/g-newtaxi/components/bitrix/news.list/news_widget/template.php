<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>

	<?if (count($arResult["ITEMS"]) != 0):?>	

		<h2>
	        <a href="<?=SITE_DIR?>news/" class="partial-underline">
	            <span class="sprite checkers-icon"></span>
	            <span class="underline"><?= tr('news')?></span>
	        </a>
	    </h2>
		   
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
									<span class="news-date"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?> | </span>
								<?endif?>
								<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a>
							<?else:?>
								<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
									<span class="news-date"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?> | </span>
								<?endif?>
								<a><?echo $arItem["NAME"]?></a>
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
			<?endforeach;?>
	<?endif;?>