<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?>

<? foreach ($arResult as $arItem): ?>
    <?
    $dataAnchor = $arItem['PARAMS']['SCROLL_TO_ANCHOR'] == 'Y' ? '#section' . $arItem['PARAMS']['SECTION_ID'] : '';
    $arItem["LINK"] = $arItem['PARAMS']['SCROLL_TO_ANCHOR'] == 'Y' ? '/#section' . $arItem['PARAMS']['SECTION_ID'] : $arItem["LINK"];
    ?>
	<li class="main-nav__left-item">
		<a href="<?= $arItem["LINK"] ?>" data-anchor="<?=$dataAnchor?>" class="main-nav__left-link <? if ($arItem["SELECTED"]): ?>active<? endif ?> <? if ($arItem["LINK"] === '/promo/'): ?>accent<? endif ?>"><?= $arItem["TEXT"] ?></a>
	</li>
<? endforeach ?>