<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

?>
<!--<pre>-->
<!--	--><? //var_dump($arResult);?>
<!--</pre>-->
<div class="profile">
	<section class="content">
		<div class="container">
			<h1>Профиль</h1>
			<form class="main-profile-data">
				<div class="order__detail-row ordertoself delivery-for" style="width: 75%;">
					<div class="text-field__wrapper half">
						<div class="text-field__name">Имя</div>
						<div class="text-field__input-wrapper">
							<input type="text" class="text-field editable edit-rofile" name="name" value="<?= $arResult['userinfo']['NAME']; ?>" readonly="readonly">
                            <?= isset($arResult['errors']['name']) ? str_replace('#ERR#', $arResult['errors']['NAME'],
                                $error) : ''; ?>
						</div>
					</div>
					<div class="text-field__wrapper half">
						<div class="text-field__name">Фамилия</div>
						<div class="text-field__input-wrapper">
							<input type="text" class="text-field editable edit-rofile" name="surname" value="<?= $arResult['userinfo']['LAST_NAME']; ?>" readonly="readonly">
                            <?= isset($arResult['errors']['surname']) ? str_replace('#ERR#',
                                $arResult['errors']['LAST_NAME'], $error) : ''; ?>
						</div>
					</div>
					<div class="text-field__wrapper half">
						<div class="text-field__name">Номер телефона</div>
						<div class="text-field__input-wrapper">
							<input type="text" class="text-field mask_phone no7" name="phone" value="+<?= $arResult['userinfo']['LOGIN']; ?>" readonly="readonly">
                            <?= isset($arResult['errors']['phone']) ? str_replace('#ERR#', $arResult['errors']['LOGIN'],
                                $error) : ''; ?>
						</div>
					</div>
					<div class="text-field__wrapper half">
						<div class="text-field__name">Email</div>
						<div class="text-field__input-wrapper">
							<input type="text" class="text-field editable edit-rofile" name="email" value="<?= $arResult['userinfo']['EMAIL']; ?>" readonly="readonly">
                            <?= isset($arResult['errors']['email']) ? str_replace('#ERR#', $arResult['errors']['email'],
                                $error) : ''; ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="order__detail-checkbox" style="padding: 0;">
						<input class="styled-checkbox" type="checkbox" id="newsletter" name="newsletter" checked/>
						<label for="newsletter"><span class="control"></span><span class="text">Получать промо-коды и другие предложения</span></label>
					</div>
				</div>
			</form>
			<hr class="wide_devider">
			<!--<pre>
			<? //var_dump($arResult); ?>
		</pre>-->
			<h3>Адреса доставки</h3>
            <? foreach ($arResult['addresses'] as $address): ?>
				<div class="row profile_addresses">
					<h4><?= $address['address']['street']; ?>
						<a href="#" class="address-edit" data-id="<?= $address['id'] ?>"></a></h4>
					<p><?= $address['address']['label'] ?></p>
					<p style="color:#A4A4A4"><?= $address['address']['comment']; ?></p>
				</div>
            <? endforeach; ?>
			<hr class="wide_devider">
			<a href="#" class="add_address main-nav__left-link add-address-btn"><span>+</span> Добавить адрес</a>
		</div>
	</section>

	<div id="modal-edit-address" class="modal" style="padding: 30px 30px !important;">

	</div>
	<div id="modal-add-address" class="modal" style="padding: 30px 30px !important;">

	</div>
</div>
<script>
	$(function () {
		new Profile({
			component: '<?=$this->getComponent()->getName()?>',
			taxiorder: taxiOrder,
			addressEditBtn: 'address-edit',
			modalShell: 'modal-edit-address',
			addressAddBtn: 'add-address-btn',
			taxiorder: taxiOrder,
			editProfileField: 'edit-rofile',
			formClass: 'main-profile-data'
		});
	});
</script>