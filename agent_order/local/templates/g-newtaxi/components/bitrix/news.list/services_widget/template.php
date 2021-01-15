<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<h2>
	<a href="<?=SITE_DIR?>services/" class="partial-underline">
		<span class="sprite checkers-icon"></span>
		<span class="underline"><?=tr('services')?></span>
	</a>
</h2>
<?if (count($arResult["ITEMS"]) != 0):?>
	<ul class="unordered-list-style">
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
					
						<a title="<?=$arItem["NAME"]?>" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
							<?echo $arItem["NAME"]?>
						</a>
				<?endif;?>	
				<?/*elseif (($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"] && $arItem["PREVIEW_TEXT"]) or ($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"] &&$arItem["DETAIL_TEXT"])):?>
					
						<?echo $arItem["NAME"]?>
					
				<?endif;*/?>
			</li>	
		<?endforeach;?>
	</ul>
<?endif;?>