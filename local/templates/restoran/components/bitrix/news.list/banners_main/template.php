<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>

<?foreach($arResult["ITEMS"] as $arItem):?>
	<? $link = ($arItem['PROPERTIES']['LINK']['VALUE']) ? $arItem['PROPERTIES']['LINK']['VALUE'] : false; ?>
	<section class="get-app">
		<div class="container">
			<? if($link):?>
				<a href="<?=$link?>" target="_blank">
			<? endif;?>
					<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>">
            <? if($link):?>
				</a>
            <? endif;?>
		</div>
	</section>
<?endforeach;?>
