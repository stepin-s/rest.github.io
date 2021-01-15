<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
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

<div class="my-orders">
    <? foreach ($arResult["ITEMS"] as $arItem): ?>
        <?
        //PR($arItem);
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

        $arProperties = array();

        $from_address = $arItem['PROPERTIES']['CITY_OTKUDA']['VALUE'] . ', ' . $arItem['PROPERTIES']['FROM']['VALUE'];
        $from_address = !empty($arItem['PROPERTIES']['FROM_HOUSE']['VALUE']) ? $from_address . ', ' . $arItem['PROPERTIES']['FROM_HOUSE']['VALUE'] : $from_address;
        $from_address = !empty($arItem['PROPERTIES']['FROM_HOUSING']['VALUE']) ? $from_address . ', корпус ' . $arItem['PROPERTIES']['FROM_HOUSING']['VALUE'] : $from_address;
        $from_address = !empty($arItem['PROPERTIES']['FROM_PORCH']['VALUE']) ? $from_address . ', подъезд ' . $arItem['PROPERTIES']['FROM_PORCH']['VALUE'] : $from_address;

        $to_address = $arItem['PROPERTIES']['CITY_KUDA']['VALUE'] . ', ' . $arItem['PROPERTIES']['TO']['VALUE'];
        $to_address = !empty($arItem['PROPERTIES']['TO_HOUSE']['VALUE']) ? $to_address . ', ' . $arItem['PROPERTIES']['TO_HOUSE']['VALUE'] : $to_address;
        $to_address = !empty($arItem['PROPERTIES']['TO_HOUSING']['VALUE']) ? $to_address . ', корпус ' . $arItem['PROPERTIES']['TO_HOUSING']['VALUE'] : $to_address;
        $to_address = !empty($arItem['PROPERTIES']['TO_PORCH']['VALUE']) ? $to_address . ', подъезд ' . $arItem['PROPERTIES']['TO_PORCH']['VALUE'] : $to_address;
        ?>
        <div class="order-block" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
            <div class="number">№<?= $arItem['PROPERTIES']['ORDER_ID']['VALUE'] ?>, <?= $arItem['DISPLAY_ACTIVE_FROM'] ?></div>
            <? if ($arItem['PROPERTIES']['STATUS_ORDER']['VALUE'] == 'completed'): ?>
                <div class="complete right">Выполнен</div>
            <? else: ?>
                <div data-status="<?= $arItem['PROPERTIES']['STATUS_ORDER']['VALUE'] ?>" data-order_id="<?= $arItem['PROPERTIES']['ORDER_ID']['VALUE'] ?>" data-element_id="<?= $arItem['ID'] ?>" class="start"><?= $arItem['PROPERTIES']['STATUS_LABEL']['VALUE'] ?><span class="back refresh"></span><span>&nbsp&nbsp<img style='display:none;width:20px;height:20px' class="loading" src="<?= SITE_TEMPLATE_PATH ?>/i/loading.GIF" alt=""/></span></div>
            <? endif ?>
            <div class="row-fluid road">
                <div class="span6"><div class="a"><?= $from_address ?></div></div>
                <div class="span6"><div class="b"><?= $to_address ?></div></div>
            </div>
            <div class="row-fluid slide">
                <?
                if (!empty($arItem['PROPERTIES']['DATA']['VALUE'])):
                    $arProperties[] = $arItem['PROPERTIES']['DATA']['VALUE'];
                    ?>
                    <div class="hidden-row clearfix">
                        <div class="span3 cell-name"><?= GetMessage('PORCH_TIME') ?></div>
                        <div class="span8 value"><?= $arItem['PROPERTIES']['DATA']['VALUE'] ?></div>
                    </div>
                <? endif ?>
                <?
                if (!empty($arItem['PROPERTIES']['DRIVER']['VALUE'])):
                    $arProperties[] = $arItem['PROPERTIES']['DRIVER']['VALUE'];
                    ?>
                    <div class="hidden-row clearfix">
                        <div class="span3 cell-name"><?= GetMessage('DRIVER') ?></div>
                        <div class="span8 value"><?= $arItem['PROPERTIES']['DRIVER']['VALUE'] ?></div>
                    </div>
                <? endif ?>
                <?
                if (!empty($arItem['PROPERTIES']['CAR']['VALUE'])):
                    $arProperties[] = $arItem['PROPERTIES']['CAR']['VALUE'];
                    ?>
                    <div class="hidden-row clearfix">
                        <div class="span3 cell-name"><?= GetMessage('CAR') ?></div>
                        <div class="span8 value"><?= $arItem['PROPERTIES']['CAR']['VALUE'] ?></div>
                    </div>
                <? endif ?>
                <?
                if (!empty($arItem['PROPERTIES']['DOP']['VALUE'])):
                    $arProperties[] = $arItem['PROPERTIES']['DOP']['VALUE'];
                    $dopParameters = implode(', ', $arItem['PROPERTIES']['DOP']['VALUE']);
                    ?>
                    <div class="hidden-row clearfix">
                        <div class="span3 cell-name"><?= GetMessage('ADDITIONAL') ?></div>
                        <div class="span8 value"><?= $dopParameters ?></div>
                    </div>
                <? endif ?>
                <?
                if (!empty($arItem['PROPERTIES']['TIP_SERVICE']['VALUE'])):
                    $arProperties[] = $arItem['PROPERTIES']['TIP_SERVICE']['VALUE'];
                    ?>
                    <div class="hidden-row clearfix">
                        <div class="span3 cell-name"><?= GetMessage('TARIFF') ?></div>
                        <div class="span8 value"><?= $arItem['PROPERTIES']['TIP_SERVICE']['VALUE'] ?></div>
                    </div>
                <? endif ?>
                <?
                if (!empty($arItem['PROPERTIES']['PORCH_TIME']['VALUE']) && $arItem['PROPERTIES']['STATUS_ORDER']['VALUE'] == 'car_assigned'):
                    $arProperties[] = $arItem['PROPERTIES']['PORCH_TIME']['VALUE'];
                    ?>
                    <div class="hidden-row clearfix">
                        <div class="span3 cell-name"><?= GetMessage('PORCH_TIME') ?></div>
                        <div class="span8 value"><?= $arItem['PROPERTIES']['PORCH_TIME']['VALUE'] ?></div>
                    </div>
                <? endif ?>

                <?
                if (!empty($arItem['PROPERTIES']['TOTAL_PRICE']['VALUE'])):
                    $arProperties[] = $arItem['PROPERTIES']['TOTAL_PRICE']['VALUE'];
                    ?>
                    <div class="hidden-row clearfix">
                        <div class="span3 cell-name"><?= GetMessage('TOTAL_PRICE') ?></div>
                        <div class="span8 value"><?= $arItem['PROPERTIES']['TOTAL_PRICE']['VALUE'] ?></div>
                    </div>
                <? endif ?>
                <?
                if (!empty($arItem['PREVIEW_TEXT'])):
                    $arProperties[] = $arItem['PREVIEW_TEXT'];
                    ?>
                    <div class="hidden-row clearfix">
                        <div class="span3 cell-name"><?= GetMessage('ODRER_INFO') ?></div>
                        <div class="span8 value"><?= $arItem['PREVIEW_TEXT'] ?></div>
                    </div>
                <? endif ?>
                <?
                if (!empty($arItem['PROPERTIES']['COMM']['VALUE'])):
                    $arProperties[] = $arItem['PROPERTIES']['COMM']['VALUE'];
                    ?>
                    <div class="hidden-row clearfix">
                        <div class="span3 cell-name"><?= GetMessage('COMM') ?></div>
                        <div class="span8 value"><?= $arItem['PROPERTIES']['COMM']['VALUE'] ?></div>
                    </div> 
                <? endif ?>
            </div>
            <? if ($arItem['PROPERTIES']['STATUS_ORDER']['VALUE'] == 'completed' || $arItem['PROPERTIES']['STATUS_ORDER']['VALUE'] == 'rejected'): ?>
                <a href="" class="delete" data-id="<?= $arItem['ID'] ?>"><?= GetMessage('REMOVE') ?></a>
            <? endif ?>
            <? if ($arItem['PROPERTIES']['STATUS_ORDER']['VALUE'] !== 'completed' && $arItem['PROPERTIES']['STATUS_ORDER']['VALUE'] !== 'rejected'): ?>
                <a href="" class="cancel" data-id="<?= $arItem['PROPERTIES']['ORDER_ID']['VALUE'] ?>"><?= GetMessage('CANCEL') ?></a>
            <? endif ?>
            <a href="<?= SITE_DIR ?>" class="right repeat"><span><?= GetMessage('REPEAT') ?></span></a>
            <form name="repeat_order" method="post" action="<?= SITE_DIR ?>" style="display: none;">
                <input type="hidden" name="CITY_OTKUDA" value="<?= $arItem['PROPERTIES']['CITY_OTKUDA']['VALUE'] ?>" />
                <input type="hidden" name="FROM" value="<?= $arItem['PROPERTIES']['FROM']['VALUE'] ?>" />
                <input type="hidden" name="FROM_HOUSE" value="<?= $arItem['PROPERTIES']['FROM_HOUSE']['VALUE'] ?>" />
                <input type="hidden" name="FROM_HOUSING" value="<?= $arItem['PROPERTIES']['FROM_HOUSING']['VALUE'] ?>" />
                <input type="hidden" name="FROM_PORCH" value="<?= $arItem['PROPERTIES']['FROM_PORCH']['VALUE'] ?>" />

                <input type="hidden" name="CITY_KUDA" value="<?= $arItem['PROPERTIES']['CITY_KUDA']['VALUE'] ?>" />
                <input type="hidden" name="TO" value="<?= $arItem['PROPERTIES']['TO']['VALUE'] ?>" />
                <input type="hidden" name="TO_HOUSE" value="<?= $arItem['PROPERTIES']['TO_HOUSE']['VALUE'] ?>" />
                <input type="hidden" name="TO_HOUSING" value="<?= $arItem['PROPERTIES']['TO_HOUSING']['VALUE'] ?>" />
                <input type="hidden" name="TO_PORCH" value="<?= $arItem['PROPERTIES']['TO_PORCH']['VALUE'] ?>" />

                <input type="hidden" name="COMM" value="<?= $arItem['PROPERTIES']['COMM']['VALUE'] ?>" />
                <input type="hidden" name="DOP" value="<?= $dopParameters ?>" />
                <input type="hidden" name="FIO" value="<?= $USER->GetFirstName() ?>" />
                <input type="hidden" name="TEL" value="<?= $USER->GetLogin() ?>" />
            </form>
        </div>
    <? endforeach; ?>
    <? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
        <?= $arResult["NAV_STRING"] ?>
    <? endif; ?>
</div>