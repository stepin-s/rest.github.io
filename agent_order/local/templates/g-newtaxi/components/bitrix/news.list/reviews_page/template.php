<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>

<div class="reviews-wrap">
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>

		<div class="reww" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
            <div class="str_one">
                <div class="estimates-stars">
					<?for($i=0;$i<5;$i++):?>
						<span class="sprite star-small <?if($arItem["PROPERTY_32"]>$i) echo 'active'?>"></span>
					<?endfor;?>
	            </div>
                <div class="str_one_l">
                    <div style="font-style: italic!important; font-weight: normal!important;">
	 					<?echo $arItem["PROPERTY_29"];?>

		                <?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
							<?echo $arItem["DISPLAY_ACTIVE_FROM"]?>
						<?endif?>
	                </div>
	            </div>
	        </div>
			<p style="font-weight: bold;"><?echo $arItem["PROPERTY_31"];?></p>

			<?/*
			if($arItem["DETAIL_TEXT"])
			{
				?>
			            <div class="adm_ans">
			            	<div class="adm_str_one" style="font-style: italic!important;
			    font-weight: normal!important; color: #797979; margin-bottom: 10px;">
			<?echo $arItem["PROPERTIES"]["ANSWER_NAME"]["VALUE"] ? $arItem["PROPERTIES"]["ANSWER_NAME"]["VALUE"] : $arItem["PROPERTIES"]["ANSWER_NAME"]["DEFAULT_VALUE"];?>, <?
				echo date("d.m.Y", strtotime($arItem["PROPERTIES"]["ANSWER_DATE"]["VALUE"]));?></div>

				<p><?
				echo $arItem["DETAIL_TEXT"];?>
			</p></div>
			<?

			}*/
			?>			
		</div>
	<?endforeach;?>
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>