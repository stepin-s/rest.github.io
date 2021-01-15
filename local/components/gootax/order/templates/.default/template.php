<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);



// uasort($arResult['cart2']['bxcart'],function ($a, $b) {
// 	$_a = trim(mb_strtolower($a['NAME'],'utf-8'));
// 	$_b = trim(mb_strtolower($b['NAME'],'utf-8'));
// 	if ($_a == $_b) {
//         return 0;
//     }
// 	// return ($_a > $_b);
// 	return ($_a < $_b) ? -1 : 1;
// });

$cart = $arResult['cart2'];
// echo("<script>console.log('php_array: ".json_encode($cart )."');</script>");
echo("<script>console.log('php_array: ".json_encode($arResult['cart2'])."');</script>");

// $this->addExternalJS("https://cdnjs.cloudflare.com/ajax/libs/mustache.js/4.0.1/mustache.js");
// require_once $_SERVER["DOCUMENT_ROOT"] . "/include/taxiorder/TaxiOrder.php";
// TaxiOrder::init();
?>
<script>
function check() {
  if ($('#zagol').val() != '')     $('#zag').removeAttr('disabled');   else
    $('#zag').attr('disabled','disable'); }
</script>
<div class="order-form">
	<section class="cart cartwrap">
		<div class="container">
			<form id="cartform">
			<div class="cart__inner">
				<h1>Корзина</h1>
				<?if(count($cart['cart'])==0):?>
				<div class="cart__empty">
					В корзине пока ничего нет! <a class="link" href="/">Вернуться в меню</a>
				</div>
				<?else:?>

				<div class="cart__items">
				
					<?
						foreach ($cart['cart'] as $key=>$item):
						$additiveItem = explode (",", $item['additive_array']);
                        $key = explode("_",$key)[0];
						$cost = floatval($cart['bxcart'][$key]['TYPES'][$item['type_id']]['cost']);
						

						?>
                            <div class="cart__item js-item" data-id="<?=$key;?>" data-type="<?=$item['type_id']?>">
                                <div class="cart__item-img" style="background-image:url(<?=$cart['bxcart'][$key]['IMAGE'];?>);" data-img="<?=$cart['bxcart'][$key]['IMAGE'];?>">
                                    <div class="badge" data-badge="{{params.badge}}">
                                        <svg class="icon">
                                            <use xlink:href="#{{params.badge}}"></use>
                                        </svg>
                                    </div>
                                </div>
                                <div class="cart__item-text">
                                    <div class="cart__item-name">
                                        <?=$cart['bxcart'][$key]['NAME'];?>
                                    </div>
                                    <div class="cart__item-option" data-id="<?=$item['type_id']?>" data-name="<?=$cart['bxcart'][$key]['TYPES'][$item['type_id']]['name'];?>" data-cost="<?=$cart['bxcart'][$key]['TYPES'][$item['type_id']]['cost'];?>" data-description="{{type.description}}">
                                        <?=$cart['bxcart'][$key]['TYPES'][$item['type_id']]['name'];?>
                                    </div>
                                    <?if(count($additiveItem)>0 && current($additiveItem) != ''):?>
                                    <div class="cart__item-wishes-header">
                                        Пожелания
                                    </div>
                                    <div class="cart__item-wishes">
                                        <ul class="comma-list">
                                            <?foreach ($additiveItem as $addKey=>$addItem):?>
                                            <li data-id="<?=$addItem?>" data-name="<?=$cart['bxcart'][$key]['DOPS'][$addItem]['name']?>" data-cost="<?=$cart['bxcart'][$key]['DOPS'][$addItem]['cost']?>">
                                                <?=$cart['bxcart'][$key]['DOPS'][$addItem]['name']?>
	                                            <?$cost+=floatval($cart['bxcart'][$key]['DOPS'][$addItem]['cost']);?>
                                            </li>
                                            <?endforeach;?>
                                        </ul>
                                    </div>
                                    <?endif;?>
                                </div>
	                            <div class="cart__item-controls">
	                                <div class="cart__item-quantity">
	                                    <a href="#" class="goods__item-control js-basket-remove">
	                                        <svg class="icon icon-remove">
	                                            <use xlink:href="#remove"></use>
	                                        </svg>
	                                    </a>
	                                    <span class="quantity"><?=$item['quantity'];?></span>
	                                    <a href="#" class="goods__item-control js-basket-add">
	                                        <svg class="icon icon-add">
	                                            <use xlink:href="#add"></use>
	                                        </svg>
	                                    </a>
	                                </div>
	                                <div class="cart__item-price"><span><?=$cost*$item['quantity'];?></span> ₽</div>
	                                <div class="cart__item-delete">
	                                    <a href="#" class="js-basket-delete-link">
	                                        <svg class="icon icon-delete">
	                                            <use xlink:href="#delete"></use>
	                                        </svg>
	                                    </a>
	                                </div>
	                            </div>
                            </div>
					<?endforeach;?>
				</div>

				<div class="row address-error minorder">
					<input type="hidden" name="minorder" value="<?=$arResult['min_line']['mincost']?>">
					<span><?if($arResult['min_line']['need']!== true ) echo 'Для доставки закажите еще на '.$arResult['min_line']['need'].' ₽ или выберите самовывоз';?></span>
				</div>
                <?endif;?>
				<div class="cart__item promo" style="display: none;">
					<div class="promocode">
						<div class="promocode__header">
							Введите промо-код
						</div>
						<div class="promocode__main">
							<div class="promocode__input">
								<input type="text" class="text-field" id="promocode">
							</div>
							<a href="#" class="button--bordered contrast" id="promocode-check">
								<span>Применить</span>
							</a>
						</div>
					</div>
				</div>
				<div class="cart__summary">
					<div class="cart__summary-header">Итого без доставки:</div>
					<div class="cart__summary-sum del_step1_sum"><span><?=$cart['cost'];?></span> ₽</div>
				</div>
				<input type="hidden" name="street" value="" id="street">
				<input type="hidden" name="house" value="" id="house">
				<input type="hidden" name="porch" value="">
				<input type="hidden" name="flat" value="">
				<input type="hidden" name="lat" value="">
				<input type="hidden" name="lon" value="">
				<input type="hidden" name="delivery-type" value="">
				<input type="hidden" name="ordercost" value="">
				<input type="hidden" name="comment" value="">
				<div class="cart__controls mb-10">
				<a href="/" class="button--bordered contrast grey">
						<span>Вернуться в меню</span>
					</a>
					<a href="#" class="button order-step2 <?=(!$USER->IsAuthorized()) ? 'needauth' : '';?>">
						<span>Оформить</span>
					</a>
				</div>
			</div>
			</form>
		</div>
	</section>
</div>
<!-- -->
<div id="modal-delivery" class="modal delivery-modal">
	<div class="modal-delivery-inner">
	<h3>Доставка</h3>
	<div id="tabs">
		<div class="preloader"></div>
		<ul class="nav-tabs">
			<li><a href="#tabs-1">Курьер</a></li>
			<li><a href="#tabs-2">Самовывоз</a></li>
		</ul>
		<div id="tabs-1">
			<form autocomplete="off">
				<div class="row">
					<div class="new">
					</div>
                    <? foreach ($arResult['client_addresses'] as $client_address):
                        $city = $client_address['address']['city'];
                        $street = $client_address['address']['street'];
                        $house = strlen($client_address['address']['house']) > 0 ? 'д. ' . $client_address['address']['house'] : '';
                        $lat = $client_address['address']['lat'];
                        $lon = $client_address['address']['lon'];
                        $addressline = $street . ' ' . $house;
                        ?>
						<div class="order__detail-checkbox">
							<input class="styled-radio"
							       type="radio"
							       id="client_address<?= $client_address['id'] ?>"
							       name="address"
							       value="<?= $client_address['id'] ?>"
							       data-address="<?= $addressline; ?>"
							       data-type="Доставка"
							       data-city="<?= $city ?>"
							       data-street="<?= $street ?>"
							       data-house="<?= $house ?>"
							       data-lat="<?= $lat ?>"
							       data-lon="<?= $lon ?>"
							       data-method="delivery"
							/>
							<label for="client_address<?= $client_address['id']; ?>">
								<span class="control"></span>
								<span class="text"><?= $client_address['address']['street'] . ' ' . $client_address['address']['house']; ?></span>
							</label>
						</div>
                    <? endforeach; ?>
					<div class="order__detail-checkbox" style="<?=(count($arResult['client_addresses'])!=0) ? 'display: block;' : 'display: none;';?>">
						<input class="styled-radio <?=(count($arResult['client_addresses'])==0) ? 'noone' : '';?>" type="radio" id="addnewaddress" name="address" data-action="addnewaddress" />
						<label for="addnewaddress">
							<span class="control"></span>
							<span class="text">Добавить новый адрес</span>
						</label>
					</div>
				</div>

				<!-- форма добавления нового адреса -->

				<div class="row newaddressform" style="<?=(count($arResult['client_addresses'])==0) ? 'display: block;' : 'display: none;';?>">
					<div class="order__detail-row need-margin-10">
						<div class="text-field__wrapper long" style="width: 100%;">
							<div class="text-field__name">Улица, дом</div>
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
													class="input-style sp6_inp_street text-field" name="street_line"
											/>
											<ul class="autocomplete_select"
											    id="<?= TaxiOrder::Config('html.inputs.id.to.autocomplete') ?>"></ul>
										</div>
									</div>
								</div>
								<!--							<input type="text" class="text-field" name="street" value="-->
                                <? //=$arResult['data']['street'];?><!--">-->
                                <?= isset($arResult['errors']['street']) ? str_replace('#ERR#',
                                    $arResult['errors']['street'], $error) : ''; ?>
							</div>
						</div>
<!--						<div class="text-field__wrapper">-->
<!--							<div class="text-field__name">Дом</div>-->
<!--							<div class="text-field__input-wrapper">-->
<!--								<input type="text" class="text-field" name="house" value="--><?//= $arResult['data']['house']; ?><!--">-->
<!--                                --><?//= isset($arResult['errors']['house']) ? str_replace('#ERR#',
//                                    $arResult['errors']['house'], $error) : ''; ?>
<!--							</div>-->
<!--						</div>-->
						<div class="text-field__wrapper">
							<div class="text-field__name">Квартира</div>
							<div class="text-field__input-wrapper">
								<input type="text" onkeyup="check();" id="zagol" class="text-field" name="flat" value="<?= $arResult['data']['flat']; ?>">
                                <?= isset($arResult['errors']['flat']) ? str_replace('#ERR#',
                                    $arResult['errors']['flat'], $error) : ''; ?>
							</div>
						</div>
						<div class="text-field__wrapper">
							<div class="text-field__name">Подъезд</div>
							<div class="text-field__input-wrapper">
								<input type="text" class="text-field" name="entrance" value="<?= $arResult['data']['entrance']; ?>">
                                <?= isset($arResult['errors']['entrance']) ? str_replace('#ERR#',
                                    $arResult['errors']['entrance'], $error) : ''; ?>
							</div>
						</div>
<!--						<div class="text-field__wrapper">-->
<!--							<div class="text-field__name">Этаж</div>-->
<!--							<div class="text-field__input-wrapper">-->
<!--								<input type="text" class="text-field" name="floar" value="--><?//= $arResult['data']['floar']; ?><!--">-->
<!--                                --><?//= isset($arResult['errors']['floar']) ? str_replace('#ERR#',
//                                    $arResult['errors']['floar'], $error) : ''; ?>
<!--							</div>-->
<!--						</div>-->
<!--						<div class="text-field__wrapper">-->
<!--							<div class="text-field__name">Домофон</div>-->
<!--							<div class="text-field__input-wrapper">-->
<!--								<input type="text" class="text-field" name="room" value="--><?//= $arResult['data']['room']; ?><!--">-->
<!--                                --><?//= isset($arResult['errors']['room']) ? str_replace('#ERR#',
//                                    $arResult['errors']['room'], $error) : ''; ?>
<!--							</div>-->
<!--						</div>-->
						<input type="hidden" name="new-lon">
						<input type="hidden" name="new-lat">
						<div class="text-field__wrapper full comm">
							<div class="text-field__name">Комментарий</div>
							<div class="text-field__input-wrapper">
								<textarea class="text-field" name="comment" id="comment"><?= $arResult['data']['comment']; ?></textarea>

                                <?= isset($arResult['errors']['comment']) ? str_replace('#ERR#',
                                    $arResult['errors']['comment'], $error) : ''; ?>
							</div>
						</div>
					</div>
				</div>
				<!-- _форма добавления нового адреса -->
				<!-- блок стоимости доставки -->
				<div class="row order__delivery-select-price" style="margin: 0px 0 20px 0;font-weight: 500;">
					<div class="order__info-delivery">
                    <span class="order__info-delivery-text">
                        <span>Доставка: </span>
                    </span>
						<span class="order__info-delivery-price">
                        <span>  0</span> ₽
                    </span>
					</div>
					<div class="order__info-delivery">
                    <span class="order__info-delivery-text">
                        Итого c доставкой:
                    </span>
						<span class="order__info-delivery-price-total">
                        <span>  0</span> ₽
                    </span>
					</div>
				</div>
				<!-- _блок стоимости доставки -->
				<div class="row address-error taberror" style="display: none;">

				</div>
				<div class="row">
					<!-- <a href="#" class="button select_delivery"> 
						 <span>Подтвердить адрес</span> </a>  -->
						<button class="c_button" disabled="disabled" id="zag" type="button" >
						<a href="#" class="select_delivery fs-18-l">Подтвердить адрес</a> </button>
					 
				</div>
			</form>

		</div>
		<div id="tabs-2">
			<form class="flexform">
				<div class="flexone">
                <?
                $i=0;
                foreach ($arResult['tenat_addresses'] as $key => $tenat_address):
                    $city = $tenat_address['city'];
                    $street = $tenat_address['street'];
                    $house = strlen($tenat_address['house']) > 0 ? 'д. ' . $tenat_address['house'] : '';
                    $lat = $tenat_address['lat'];
                    $lon = $tenat_address['lon'];
                    $addressline = $street . ' ' . $house;
                    ?>
					<div class="row">
						<div class="order__detail-checkbox">
							<input class="styled-radio"
							       type="radio"
							       id="tenat_address<?= $tenat_address['id'] ?>"
							       name="address"
							       value="<?= $tenat_address['id'] ?>"
							       data-address="<?= $addressline; ?>"
							       data-type="Самовывоз"

							       data-city="<?= $city ?>"
							       data-street="<?= $street ?>"
							       data-house="<?= $house ?>"
							       data-lat="<?= $lat ?>"
							       data-lon="<?= $lon ?>"
							       data-method="pickup"
							       <?=$i==0 ? 'checked' : '';?>
							/>
							<label for="tenat_address<?= $tenat_address['id'] ?>">
								<span class="control"></span>
								<span class="text"><?= $tenat_address['street'] ?>
									, д <?= $tenat_address['house'] ?></span>
							</label>
						</div>
					</div>
                <?
                    $i++;
                    endforeach;
                ?>
				</div>
				<div class="row flextwo div-deliv">
					<a href="#" class="button select_delivery">
						<span class="fs-18-l">Подтвердить адрес</span>
					</a>
				</div>
			</form>
		</div>
	</div>
	<a href="#" class="close-modal2">Close</a>
</div>

<script>
	$(function () {
		new Order({
			component: '<?=$this->getComponent()->getName()?>',
			orderbtn1: '.order-step1',
			orderbtn2: '.order-step2',
			taxiorder: taxiOrder
		});
	});
</script>
