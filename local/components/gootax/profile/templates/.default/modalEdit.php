<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

?>
<div class="order__detail profile-edit" style="border: 0;margin-bottom: 0;">
	<h3 class="order__detail-header">
		Редактировать адрес
	</h3>
<!--	<pre>-->
<!--		--><?//var_dump($arResult['addresses']);?>
<!--	</pre>-->
	<form>
	<div class="order__detail-row need-margin-10">

        <?
	        require_once $_SERVER["DOCUMENT_ROOT"] . "/include/taxiorder/TaxiOrder.php";
	        TaxiOrder::init();
	        $address = $arResult['addresses']['address'];
//	        var_dump($address);
        ?>
		<!-- -->
		<div class="text-field__wrapper long" style="width: 100%;">
			<div class="text-field__name">Улица</div>
			<div class="text-field__input-wrapper">
				<div class="order-form2">
					<div class="direction" data-direction="from" style="display:none;">
						<div style="position: relative">
							<input
									type="text"
									placeholder="<?= TaxiOrder::Phrase('address') ?>"
									id="<?= TaxiOrder::Config('html.inputs.id.from.street') ?>"
									style="width:100%"
									class="input-style sp6_inp_street"
							/>
							<ul class="autocomplete_select"
							    id="<?= TaxiOrder::Config('html.inputs.id.from.autocomplete') ?>"></ul>
							<a title="<?= TaxiOrder::Phrase('findMe') ?>" href="#"
							   id="<?= TaxiOrder::Config('html.elements.id.findMe') ?>" class="find-me"
							   style="display:none; cursor: pointer;float: right;margin:5px 5px 0 -24px;"></a>
						</div>
						<input style="min-width: 26%;" type="text" placeholder="<?= TaxiOrder::Phrase('porch') ?>"
						       id="<?= TaxiOrder::Config('html.inputs.id.from.porch') ?>"
						       class="input-style sp6_inp_pod"/>
						<textarea style="display:block" id="<?= TaxiOrder::Config('html.inputs.id.from.comment') ?>"
						          class="textarea-style sp6_txt"
						          placeholder="<?= TaxiOrder::Phrase('comment') ?>"></textarea>

					</div>
					<div class="direction" data-direction="to">
						<div style="position: relative">
							<input
									style="width:100%"
									type="text"
									placeholder="<?= TaxiOrder::Phrase('address') ?>"
									id="<?= TaxiOrder::Config('html.inputs.id.to.street') ?>"
									class="input-style sp6_inp_street text-field" name="street"
									value="<?=$address['street']. ' '.$address['house']?>"
							/>
							<ul class="autocomplete_select"
							    id="<?= TaxiOrder::Config('html.inputs.id.to.autocomplete') ?>"></ul>
						</div>
					</div>
				</div>
				<!--							<input type="text" class="text-field" name="street" value="--><?//=$arResult['data']['street'];?><!--">-->
                <?=isset($arResult['errors']['street']) ? str_replace('#ERR#', $arResult['errors']['street'], $error) : '';?>
			</div>
		</div>
<!--		<div class="text-field__wrapper">-->
<!--			<div class="text-field__name">Дом</div>-->
<!--			<div class="text-field__input-wrapper">-->
<!--				<input type="text" class="text-field" name="house" value="--><?//=$arResult['data']['house'];?><!--">-->
<!--                --><?//=isset($arResult['errors']['house']) ? str_replace('#ERR#', $arResult['errors']['house'], $error) : '';?>
<!--			</div>-->
<!--		</div>-->
		<div class="text-field__wrapper">
			<div class="text-field__name">Квартира</div>
			<div class="text-field__input-wrapper">
				<input type="text" class="text-field" name="apt" value="<?=$address['apt'];?>">
                <?=isset($arResult['errors']['flat']) ? str_replace('#ERR#', $arResult['errors']['flat'], $error) : '';?>
			</div>
		</div>
		<div class="text-field__wrapper">
			<div class="text-field__name">Подъезд</div>
			<div class="text-field__input-wrapper">
				<input type="text" class="text-field" name="porch" value="<?=$address['porch'];?>">
                <?=isset($arResult['errors']['entrance']) ? str_replace('#ERR#', $arResult['errors']['entrance'], $error) : '';?>
			</div>
		</div>
<!--		<div class="text-field__wrapper">-->
<!--			<div class="text-field__name">Этаж</div>-->
<!--			<div class="text-field__input-wrapper">-->
<!--				<input type="text" class="text-field" name="floar" value="--><?//=$arResult['data']['floar'];?><!--">-->
<!--                --><?//=isset($arResult['errors']['floar']) ? str_replace('#ERR#', $arResult['errors']['floar'], $error) : '';?>
<!--			</div>-->
<!--		</div>-->
<!--		<div class="text-field__wrapper">-->
<!--			<div class="text-field__name">Домофон</div>-->
<!--			<div class="text-field__input-wrapper">-->
<!--<!--				<input type="text" class="text-field" name="room" value="--><?////=$arResult['data']['room'];?><!--<!--">-->
<!--				<select>-->
<!--					<option>Да</option>-->
<!--					<option>Нет</option>-->
<!--				</select>-->
<!--                --><?//=isset($arResult['errors']['room']) ? str_replace('#ERR#', $arResult['errors']['room'], $error) : '';?>
<!--			</div>-->
<!--		</div>-->
		<div class="text-field__wrapper full">
			<div class="text-field__name">Комментарий</div>
			<div class="text-field__input-wrapper">
				<textarea class="text-field" name="comment"><?=$address['comment'];?></textarea>
<!--				<input type="text" class="text-field" name="comment" value="--><?//=$arResult['data']['comment'];?><!--">-->
<!--                --><?//=isset($arResult['errors']['comment']) ? str_replace('#ERR#', $arResult['errors']['comment'], $error) : '';?>
			</div>
		</div>
		<input type="hidden" name="lat" value="<?=$address['lat'];?>">
		<input type="hidden" name="lon" value="<?=$address['lon'];?>">
		<input type="hidden" name="id" value="<?=$arResult['addresses']['id']?>">

		<div class="text-field__wrapper full">
			<a href="#" class="button select_delivery saveAddress">
				<span>Сохранить</span>
			</a>
		</div>

	</div>
	</form>
</div>