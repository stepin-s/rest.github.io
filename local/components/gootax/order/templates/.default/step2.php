<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
$this->addExternalJS("https://cdnjs.cloudflare.com/ajax/libs/mustache.js/4.0.1/mustache.js");
$this->addExternalJS("https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js");
$this->addExternalCss("https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css");

$error = '<span class="error">#ERR#</span>';
global $USER;
$login = $USER->GetLogin();
$cart = $arResult['cart'];
// $name_mass = array("$arResult['data']['name']", "$arResult['data']['surname']")
//echo '<pre>';
//var_dump($arResult);
//echo '</pre>';
?>
<script>
	$(document).ready(function() {
  $( ".toggle" ).click( function() {
    if ($("#image").css('transform') == 'none') {
      $("#image").css({'transform': 'rotate(-180deg)'});
    } else {
      $("#image").css({'transform': ''});
    };
  });
});

/*
function showSingleDiv(selector) {
  const prevBlockEl = document.querySelector('.single.active'),
		currBlockEl = document.querySelector(selector);
  if (!currBlockEl || prevBlockEl === currBlockEl) return;
  prevBlockEl && prevBlockEl.classList.remove('active');
  currBlockEl.classList.add('active');
}
*/

</script>


<div class="container">
	<h1 class="h1_del_step2">Оформление заказа</h1>
	<form class="order__inner" id="create-order">
		<div class="order__details-wrapper">
			<div class="order__detail">
				<?if(count($arResult['errors'])>0):?>
					<?foreach ($arResult['errors'] as $error):?>
						<p><?=$error;?></p>
					<?endforeach;?>
				<?endif;?>
				<div class="order-delivery-header">
				<h3 class="order__detail-header">
					<span class="delivery-type"><?=$arResult['data']['delivery-type'] == 'pickup' ? 'Самовывоз' : 'Доставка';?></span>
				</h3>
					<a href="#" class="editaddressline">Изменить</a>
				</div>

				<?php
					$addressLine = $arResult['data']['street'].' '.$arResult['data']['house'];
					if(strlen($arResult['data']['porch']) > 0) {
                        $addressLine .= ' п.'.$arResult['data']['porch'];
					}
	                if(strlen($arResult['data']['flat']) > 0) {
	                    $addressLine .= ', кв.'.$arResult['data']['flat'];
	                }
		            if(strlen($arResult['data']['comment']) > 0) {
		                $addressLine .= '<br/>'.$arResult['data']['comment'];
		            }
					//$addressLine += strlen($arResult['data']['porch']) > 0 ? ' п.'.$arResult['data']['porch'] : '';
                    //$addressLine += strlen($arResult['data']['flat']) > 0 ? ' п.'.$arResult['data']['flat'] : '';
				?>
				<div class="row">
					<span class="addressline"><?=$addressLine;?> </span>
				</div>
				<input type="hidden" name="street" value="<?=$arResult['data']['street'];?>">
				<input type="hidden" name="house" value="<?=$arResult['data']['house'];?>">
				<input type="hidden" name="flat" value="<?=$arResult['data']['flat'];?>">
				<input type="hidden" name="porch" value="<?=$arResult['data']['porch'];?>">
				<input type="hidden" name="lat" value="<?=$arResult['data']['lat'];?>">
				<input type="hidden" name="lon" value="<?=$arResult['data']['lon'];?>">
				<input type="hidden" name="delivery-type" value="<?=$arResult['data']['delivery-type'];?>">
				<input type="hidden" name="ordercost" value="<?=$arResult['data']['ordercost'];?>">
				<input type="hidden" name="comment" value="<?=$arResult['data']['comment'];?>">
				<!--<div class="order__detail-row">
					<div class="order__detail-checkbox">
						<input class="styled-radio" type="radio" id="delivery-type1" name="delivery-type" value="delivery" checked/>
						<label for="delivery-type1"><span class="control"></span><span class="text">Курьер</span></label>
					</div>
					<div class="order__detail-checkbox">
						<input class="styled-radio" type="radio" id="delivery-type2" name="delivery-type" value="pickup"/>
						<label for="delivery-type2"><span class="control"></span><span class="text">Самовывоз</span></label>
					</div>
				</div>-->
			</div>
			<div class="order__detail" style="display: none;">
				<h3 class="order__detail-header">
					Адрес доставки
				</h3>
				<div class="order__detail-row need-margin-10">
					<?
                        require_once $_SERVER["DOCUMENT_ROOT"] . "/include/taxiorder/TaxiOrder.php";
                        TaxiOrder::init();
					?>
					<!-- -->
					<div class="text-field__wrapper long">
						<div class="text-field__name">Улица</div>
						<div class="text-field__input-wrapper">
<!--							<div class="order-form2">-->
<!--								<div class="direction" data-direction="from" style="display:none;">-->
<!--									<div style="position: relative">-->
<!--										<input-->
<!--												type="text"-->
<!--												placeholder="--><?//= TaxiOrder::Phrase('address') ?><!--"-->
<!--												id="--><?//= TaxiOrder::Config('html.inputs.id.from.street') ?><!--"-->
<!--												style="width:100%"-->
<!--												class="input-style sp6_inp_street"-->
<!--										/>-->
<!--										<ul class="autocomplete_select"-->
<!--										    id="--><?//= TaxiOrder::Config('html.inputs.id.from.autocomplete') ?><!--"></ul>-->
<!--										<a title="--><?//= TaxiOrder::Phrase('findMe') ?><!--" href="#"-->
<!--										   id="--><?//= TaxiOrder::Config('html.elements.id.findMe') ?><!--" class="find-me"-->
<!--										   style="display:none; cursor: pointer;float: right;margin:5px 5px 0 -24px;"></a>-->
<!--									</div>-->
<!--									<input style="min-width: 26%;" type="text" placeholder="--><?//= TaxiOrder::Phrase('porch') ?><!--"-->
<!--									       id="--><?//= TaxiOrder::Config('html.inputs.id.from.porch') ?><!--"-->
<!--									       class="input-style sp6_inp_pod"/>-->
<!--									<textarea style="display:block" id="--><?//= TaxiOrder::Config('html.inputs.id.from.comment') ?><!--"-->
<!--									          class="textarea-style sp6_txt"-->
<!--									          placeholder="--><?//= TaxiOrder::Phrase('comment') ?><!--"></textarea>-->
<!---->
<!--								</div>-->
<!--								<div class="direction" data-direction="to">-->
<!--									<div style="position: relative">-->
<!--										<input-->
<!--												style="width:100%"-->
<!--												type="text"-->
<!--												placeholder="--><?//= TaxiOrder::Phrase('address') ?><!--"-->
<!--												id="--><?//= TaxiOrder::Config('html.inputs.id.to.street') ?><!--"-->
<!--												class="input-style sp6_inp_street text-field" name="street"-->
<!--										/>-->
<!--										<ul class="autocomplete_select"-->
<!--										    id="--><?//= TaxiOrder::Config('html.inputs.id.to.autocomplete') ?><!--"></ul>-->
<!--									</div>-->
<!--								</div>-->
<!--							</div>-->
<!--							<input type="text" class="text-field" name="street" value="--><?//=$arResult['data']['street'];?><!--">-->
							<span class="error"><?=isset($arResult['errors']['street']) ? str_replace('#ERR#', $arResult['errors']['street'], $error) : '';?></span>
						</div>
					</div>
					<div class="text-field__wrapper">
						<div class="text-field__name">Дом</div>
						<div class="text-field__input-wrapper">
							<input type="text" class="text-field" name="house" value="<?=$arResult['data']['house'];?>">
							<span class="error"><?=isset($arResult['errors']['house']) ? str_replace('#ERR#', $arResult['errors']['house'], $error) : '';?></span>
						</div>
					</div>
					<div class="text-field__wrapper">
						<div class="text-field__name">Квартира</div>
						<div class="text-field__input-wrapper">
							<input type="text" class="text-field" name="flat" value="<?=$arResult['data']['flat'];?>">
							<span class="error"><?=isset($arResult['errors']['flat']) ? str_replace('#ERR#', $arResult['errors']['flat'], $error) : '';?></span>
						</div>
					</div>
					<div class="text-field__wrapper">
						<div class="text-field__name">Подъезд</div>
						<div class="text-field__input-wrapper">
							<input type="text" class="text-field" name="entrance" value="<?=$arResult['data']['entrance'];?>">
							<span class="error"><?=isset($arResult['errors']['entrance']) ? str_replace('#ERR#', $arResult['errors']['entrance'], $error) : '';?></span>
						</div>
					</div>
					<div class="text-field__wrapper">
						<div class="text-field__name">Этаж</div>
						<div class="text-field__input-wrapper">
							<input type="text" class="text-field" name="floar" value="<?=$arResult['data']['floar'];?>">
							<span class="error"><?=isset($arResult['errors']['floar']) ? str_replace('#ERR#', $arResult['errors']['floar'], $error) : '';?></span>
						</div>
					</div>
					<div class="text-field__wrapper">
						<div class="text-field__name">Домофон</div>
						<div class="text-field__input-wrapper">
							<input type="text" class="text-field" name="room" value="<?=$arResult['data']['room'];?>">
							<span class="error"><?=isset($arResult['errors']['room']) ? str_replace('#ERR#', $arResult['errors']['room'], $error) : '';?></span>
						</div>
					</div>
					<div class="text-field__wrapper full">
						<div class="text-field__name">Комментарий</div>
						<div class="text-field__input-wrapper">
							<input type="text" class="text-field" name="comment2" value="<?=$arResult['data']['comment'];?>">
							<span class="error"><?=isset($arResult['errors']['comment']) ? str_replace('#ERR#', $arResult['errors']['comment'], $error) : '';?></span>
						</div>
					</div>
				</div>
			</div>
<!--			<pre>-->
				<?//var_dump($arResult['tenat_addresses']);?>
<!--			</pre>-->
			<div class="order__detail">
				<h3 class="order__detail-header">
					Личные данные
				</h3>
				<div class="order__detail-row">
					<div class="order__detail-checkbox">
						<input class="styled-radio" type="radio" id="delivery-for1" name="delivery-for" value="ordertoself" <?=($arResult['data']['delivery-for']=='ordertoself' || !isset($arResult['data']['delivery-for'])) ? 'checked' : ''?>/>
						<label for="delivery-for1"><span class="control"></span><span class="text">Заказ себе</span></label>
					</div>
					<div class="order__detail-checkbox">
						<input class="styled-radio" type="radio" id="delivery-for2" name="delivery-for" value="ordertoanother" <?=($arResult['data']['delivery-for']=='ordertoanother') ? 'checked' : ''?>/>
						<label for="delivery-for2"><span class="control"></span><span class="text">Заказ другу</span></label>
					</div>
				</div>
				<div class="order__detail-row ordertoself delivery-for" style="display: <?=$arResult['data']['delivery-for']=='ordertoself' ||$arResult['data']['delivery-for']=='' ? 'flex;' : 'none;';?>">
					<div class="text-field__wrapper half">
						<div class="text-field__name">Имя, фамилия</div>
						<div class="text-field__input-wrapper">
							<input type="text" class="text-field" name="name" value="<?=$arResult['data']['name'], '  ' , $arResult['data']['surname'];?>" <?=strlen($arResult['data']['name']) > 0 ? 'readonly="readonly"' : '';?>>
							<span class="error"><?=isset($arResult['errors']['name']) ? str_replace('#ERR#', $arResult['errors']['name'], $error) : '';?></span>
							<input type="text" class="text-field" hidden="true"  name="surname" value="<?=$arResult['data']['surname'];?>" <?=strlen($arResult['data']['surname']) > 0 ? 'readonly="readonly"' : '';?>>
							<span class="error"  ><?=isset($arResult['errors']['surname']) ? str_replace('#ERR#', $arResult['errors']['surname'], $error) : '';?></span>
						</div>
					</div>

					<div class="text-field__wrapper half">
						<div class="text-field__name">Номер телефона</div>
						<div class="text-field__input-wrapper">
							<input type="text" class="text-field mask_phone" name="phone" value="<? echo (strlen($login)>0 ? $login : '');?>" readonly="readonly">
							<span class="error"><?=isset($arResult['errors']['phone']) ? str_replace('#ERR#', $arResult['errors']['phone'], $error) : '';?></span>
						</div>
					</div>
				</div>
				<div class="order__detail-row ordertoanother delivery-for" style="display: <?=$arResult['data']['delivery-for']=='ordertoanother' ? 'flex;' : 'none;';?>">
					<div class="text-field__wrapper half">
						<div class="text-field__name">Имя, фамилия</div>
						<div class="text-field__input-wrapper">
							<input type="text" class="text-field" name="another_name" value="<?=$arResult['data']['another_name'], '  ' , $arResult['data']['another_surname'];?>">
							<span class="error"><?=isset($arResult['errors']['another_name']) ? str_replace('#ERR#', $arResult['errors']['another_name'], $error) : '';?></span>
						</div>
					</div>
					<div class="text-field__wrapper half">
						<div class="text-field__name">Номер телефона</div>
						<div class="text-field__input-wrapper">
							<input type="text" class="text-field mask_phone" name="another_phone" value="<?=$arResult['data']['another_phone'];?>">
							<span class="error"><?=isset($arResult['errors']['another_phone']) ? str_replace('#ERR#', $arResult['errors']['another_phone'], $error) : '';?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="order__detail">
				<h3 class="order__detail-header">
					Время доставки
				</h3>
				<div class="order__detail-row">
					<!-- <div class="order__detail-checkbox">
						<input class="styled-radio" type="radio" id="delivery-time1" name="delivery-time" value="" checked/>
						<label for="delivery-time1"><span class="control"></span><span class="text">Сейчас</span></label>
					</div>
					<div class="order__detail-checkbox c_time">
						<input class="styled-radio pick_time" type="radio" id="delivery-time2" name="delivery-time" value="wish-time"/>
						<label for="delivery-time2"><span class="control"></span><span class="text">Указать желаемое время</span></label>
					</div> -->
				</div>

				<div class="order__detail-row pick_time_block"  > <!-- style="display: none;" -->
				<div class="text-field__wrapper half">
						<div class="text-field__input-wrapper">
						<select id="main" name="main" class="main_field" aria-required="true">
							<option value="today" selected="selected">Сегодня</option>
							<option value="tomorrow" >Завтра</option>
						</select>
						</div>
				</div>
					<div class="hide today text-field__wrapper half " >
						<!-- <div class="text-field__name">Желаемое время</div> -->
						<div class="text-field__input-wrapper">
							<?php
                            $period = $arResult['tenat_addresses'][0]['period'];
                            $periods = explode("-", $period);

                            $startTime = new \DateTime($periods[0].":00");
                            $endTime = new \DateTime($periods[1].":00");


                            echo '<select name="wish-time" class="wish-time">';
                            // echo '<option value="">Выберите время</option>';
                            while ($startTime < $endTime) {
                                $dateNow = new \DateTime();
                                if($startTime > $dateNow->modify('+60 minutes'))
                                    echo '<option value="'.$startTime->format('U').'">'.$startTime->format('H:i').' </option>';
                                $startTime->modify('+30 minutes')->format('H:i');
							}
							
							/*
                            $startTime = new \DateTime($periods[0].":00");
                            $endTime = new \DateTime($periods[1].":00");
                            $dateNow = new \DateTime();
                            $dateNow->modify('+1 day');
                            $endTime->modify('+1 day');
                            while ($startTime < $endTime) {
                                echo '<option value="'.$startTime->format('U').'">'.$startTime->format('H:i').', Завтра</option>';
                                $startTime->modify('+30 minutes')->format('H:i');
							}
							*/
							echo '</select>';
							?>
<!--							<input type="text" class="text-field" name="pick_time" value="">-->
<!--                            --><?//=isset($arResult['errors']['pick_time']) ? str_replace('#ERR#', $arResult['errors']['pick_time'], $error) : '';?>
						</div>
					</div>
					
					<div class="hide tomorrow text-field__wrapper half " >
						<div class="text-field__input-wrapper">


						<?php 
                            $startTime = new \DateTime($periods[0].":00");
                            $endTime = new \DateTime($periods[1].":00");
                            $dateNow = new \DateTime();
							echo '<select name="wish-time-tom" class="wish-time">';
                            while ($startTime < $endTime) {
                                echo '<option value="'.$startTime->format('U').'">'.$startTime->format('H:i').' </option>';
								$startTime->modify('+30 minutes')->format('H:i');
							}
							echo '</select>';
							?>
							</div>
							</div>
							</div>
			</div>
			<div class="order__detail no-bd">
				<h3 class="order__detail-header">
					Оплата
				</h3>
				<div class="order__detail-row">
<!--					<div class="order__detail-checkbox">-->
<!--						<input class="styled-radio" type="radio" id="payment-type1" name="payment-type" checked value="online"/>-->
<!--						<label for="payment-type1"><span class="control"></span><span class="text">Оплата онлайн</span></label>-->
<!--					</div>-->
					<div class="order__detail-checkbox">
						<input class="styled-radio" type="radio" id="payment-type3" name="payment-type" value="CASH" checked/>
						<label for="payment-type3"><span class="control"></span><span class="text">Наличными</span></label>
					</div>
					<div class="order__detail-checkbox">
						<input class="styled-radio" type="radio" id="payment-type2" name="payment-type" value="CARD_TERMINAL"/>
						<label for="payment-type2"><span class="control"></span><span class="text">Картой при получении</span></label>
					</div>
				</div>
			</div>
			<div class="cart__controls vis-md nopad_del_step2">
				<input name="cart" type="hidden" value="">
				<a href="/cart_new/" class="button button--bordered contrast grey order-step11">
					<span>Назад в корзину</span>
				</a>
				<a href="#" class="button order-step33 create-order">
					<span>Заказать</span>
				</a>
			</div>
		</div>
		<div class="order__info-wrapper">
			<div class="order__info">
				<div class="order__info-header mb-0">
					<span>Состав заказа</span>
					<a href="#" id="image" class="toggle order__info-toggle">
						<svg class="icon icon-arrow-down grey" style="transform: rotate(180deg);">
							<use xlink:href="#arrow-down"></use>
						</svg>
					</a>
				</div>
				<div class="order__info-toggle-div" style="margin-top: 15px;">

				<ul class="order__info-list cart__items">

                    <?foreach ($cart['cart'] as $key=>$item):
                        $key = explode("_",$key)[0];
                    $additiveItem = explode (",", $item['additive_array']);
                    $cost = floatval($cart['bxcart'][$key]['TYPES'][$item['type_id']]['cost']);
                    ?>
							<li class="order__info-item">
								
								<div class="order__info-text">
									<div class="order__info-name">
                                        <?=$cart['bxcart'][$key]['NAME'];?>
										<p><?=$cart['bxcart'][$key]['TYPES'][$item['type_id']]['name'];?>
										<span class="order__info-quantity">x&nbsp;<?=$item['quantity'];?></span></p>
									</div>
                                    <?if(count($additiveItem)>0 && current($additiveItem) != ''):?>
									<div class="order__info-wishes-header">
										Пожелания
									</div>

									<div class="order__info-text-wishes">
                                    <?foreach ($additiveItem as $addKey=>$addItem):?>
                                        <?=$cart['bxcart'][$key]['DOPS'][$addItem]['name']?> <?=$cart['bxcart'][$key]['DOPS'][$addItem]['cost']?> ₽
                                        <?$cost+=floatval($cart['bxcart'][$key]['DOPS'][$addItem]['cost']);?>
                                    <?endforeach;?>
									</div>
									<?endif;?>
								</div>
								<div class="order__info-quantity" hidden="true">
									
								</div>
								
								<div class="order__info-price"><?=$cost*$item['quantity'];?> ₽</div>
							</li>
                    <?endforeach;?>



				</ul>
				<div class="order__info-delivery">
                    <span class="order__info-delivery-text">
                        Итого без доставки:
                    </span>
					<span class="order__info-delivery-price-without cart__summary-sum"><span><?=$cart['cost']?></span> ₽</span>
				</div>
				<div class="order__info-delivery">
                    <span class="order__info-delivery-text">
                        <span>Доставка</span>:
                    </span>
					<span class="order__info-delivery-price">
                        <span><?=$arResult['data']['ordercost'] > 0 ? $arResult['data']['ordercost'] : 0;?></span> ₽
                    </span>
				</div>
				<div class="order__info-delivery">
                    <span class="order__info-delivery-text">
                        Итого c доставкой:
                    </span>
					<span class="order__info-delivery-price-total">
                        <span><?=$arResult['data']['ordercost'] > 0 ? $arResult['data']['ordercost'] + $cart['cost'] : $cart['cost'];?></span> ₽
                    </span>
				</div>
			</div>
			</div>
			<div class="cart__controls vis-sm">
				<input name="cart" type="hidden" value="">
				<a href="/cart_new/" class="button button--bordered contrast grey order-step11">
					<span>Назад в корзину</span>
				</a>
				<a href="#" class="button order-step33 create-order">
					<span>Заказать</span>
				</a>
			</div>
		</div>
	</form>
</div>
</div>

<script>
	(function() {
		var select = document.querySelector('#main'),
		hide = document.querySelectorAll('.hide');
		var change = function() {
			[].forEach.call(hide, function(el) {
				var add = el.classList.contains(select.value) ? "add" : "remove";
				el.classList[add]('show');
			});
		}
		select.addEventListener('change', change);
		change()
})();
// window.addEventListener('DOMContentLoaded', );
</script>