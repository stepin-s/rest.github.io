<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<section class="slider">
    <div class="container" style="padding: 0; max-width: 966px;">
        <div class="slick">
        	<?foreach($arResult["ITEMS"] as $arItem):?>
        		<?if ($arItem['PROPERTIES']['LINK']['VALUE']) :?>
		            <a href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>" title="<?=$arItem['NAME']?>" class="slider-img" style="background-image: url(<?=$arItem['PREVIEW_PICTURE']['SRC']?>);" ></a>
		            <?else:?>
		            <div title="<?=$arItem['NAME']?>" class="slider-img" style="background-image: url(<?=$arItem['PREVIEW_PICTURE']['SRC']?>);" ></div>
	            <?endif;?>
			<?endforeach;?>
        </div>
    </div>
</section>
