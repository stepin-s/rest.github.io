<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$this->addExternalJS("https://cdnjs.cloudflare.com/ajax/libs/mustache.js/4.0.1/mustache.js");
?>
<?
$orderData = $arResult['response']['formdata'];
$house = strlen($orderData['house']) > 0 ? ' '.$orderData['house'] : '';
$porch = strlen($orderData['porch']) > 0 ? 'п. '.$orderData['porch'] : '';
$entrance = strlen($orderData['entrance']) > 0 ? 'п. '.$orderData['entrance'] : '';
$flat = strlen($orderData['flat']) > 0 ? ', кв. '.$orderData['flat'] : '';
$floar = strlen($orderData['floar']) > 0 ? 'эт. '.$orderData['floar'] : '';


$order = json_decode($arResult['response']['response']);
$orderId = $order->result->order_number;

$cart = $arResult['cart'];

?>

<script>
	$(document).ready(function() {
  $( ".toggle" ).click( function() {
    console.log($("#image").css('transform'));
    if ($("#image").css('transform') == 'none') {
      $("#image").css({'transform': 'rotate(-180deg)'});
    } else {
      $("#image").css({'transform': ''});
    };
  });
});
</script>

<div class="container">
	<h1>Заказ №<?=$orderId//$arResult['order_id'];?> принят</h1>
	<div class="order__inner">
		<div class="order__details-wrapper ">

			<div class="order__detail">
				<div class="order__detail-text-row">
					<div class="order__detail-text-left">
						Статус заказа:
					</div>
					<div class="order__detail-text-right">
						<span class="orderstatus" data-orderid="<?=$order->result->order_id?>">Создан</span>
					</div>
				</div>
				<div class="order__detail-text-row">
					<div class="order__detail-text-left">
						<?=($orderData['delivery-type']=='pickup') ? 'Самовывоз:' : 'Адрес доставки:';?>
					</div>
					<div class="order__detail-text-right">
						<? echo $orderData['street'].' '.$house.' '.$porch.' '.$entrance.' '.$floar.' '.$flat;?>
						<!--г. Ижевск, ул. Героя России Ильфата Закирова, д.22, п. 4, эт 3, кв. 131-->
					</div>
				</div>
				<div class="order__detail-text-row">
					<div class="order__detail-text-left">
						Предварительное время доставки:
					</div>
					<div class="order__detail-text-right">
						00:00
					</div>
				</div>
				<div class="order__detail-text-row">
					<div class="order__detail-text-left">
					<?=($orderData['comment']!="") ? 'Комментарий:' : '';?>
					</div>
					<div class="order__detail-text-right">
						<?=$orderData['comment'];?>
					</div>
				</div>

			</div>

			<div class="order__detail no-bd">
				<div class="pb-10">
					Для отмены или корректировки заказа свяжитесь с диспетчером по телефону 8 800 777-22-22
				</div>
			</div>
		</div>
		<div class="order__info-wrapper">
			<div class="order__info">
				<div class="order__info-header">
					<span>Состав заказа</span>
					<a href="#" id="image" class="toggle order__info-toggle">
						<svg class="icon icon-arrow-down grey">
							<use xlink:href="#arrow-down"></use>
						</svg>
					</a>
				</div>
				<div class="order__info-toggle-div" style="margin-top: 15px;">
				<ul class="order__info-list cart__items">
<!--					<script id="template" type="x-tmpl-mustache">-->
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
					<span class="order__info-delivery-price cart__summary-sum2">
                        <span><?=intval($orderData['total'])?></span> ₽
                    </span>
				</div>

				<div class="order__info-delivery">
                    <span class="order__info-delivery-text">
                        <span>Доставка</span>:
                    </span>
					<span class="order__info-delivery-price">
                        <span><?=$orderData['ordercost']> 0 ? $orderData['ordercost']   : 0 ;?></span> ₽
                    </span>

				</div>
				<div class="order__info-delivery">
                    <span class="order__info-delivery-text">
                        Итого c доставкой:
                    </span>
					<span class="order__info-delivery-price-total">
                        <span><? echo intval($orderData['ordercost']) + intval($orderData['total']);?></span> ₽
                    </span>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="order__footer cart__controls no-bd back-to-menu">
		<a href="/" class="button step_3_back_button">
			<span>Вернуться в меню</span>
		</a>
	</div>
</div>
<script>

	//order_id
</script>