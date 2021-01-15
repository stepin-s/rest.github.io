function Order($params) {
	const params = $params;
	const wrap = $('.cartwrap');
	this.modal = false;
	this.timer;
	this.interval = 30000;

	this.cartItems = false;

	if (wrap.length) {

		const methods = {
			init: function () {
				$("#modal-delivery").tabs();
				$.modal.defaults.clickClose = false;
				$.modal.defaults.closeExisting = false;

				if (window.location.href.indexOf("delivery") > -1) {
					methods.displayDeliveryModal();
				}

				/**
				 * Выбираем чекбокс добавить новый адрес
				 */
				$(document).on("click", 'input[data-action="addnewaddress"]', function (e) {
					if($(e.target).is(':checked'))
						$('.newaddressform').show();
					else {
						if($(e.target).hasClass('noone') == false)
						$('.newaddressform').hide();
					}
				});

				$(document).on("click", '.order__info-toggle', function (e) {
					e.preventDefault();
					$('.order__info-toggle').toggleClass('opened');
					$('.order__info-toggle-div').toggle();
				});

				/**
				 * Выбираем адрес доставки
				 */
				$(document).on("click", '[name="address"]', function (e) {
					if(!$(this).data('action')) {
						methods.getDateAttsFromEl(e.target);
						//$('.newaddressform').hide();
						$('.order__delivery-select-price').show();
					}
					else {
						//$('#tabs').removeClass('bisy');
						$('.button.select_delivery').removeClass('disabled');
						$('.address-error').hide();
						$('.order__delivery-select-price').hide();
					}
				});

				/**
				 * Изменения способа доставки
				 */
				$(document).on("click", '.editaddressline', function (e) {
					/*$('input[name="street"]').val('');
					$('input[name="street"]').attr('data-city','');
					$('input[name="street"]').attr('data-lat','');
					$('input[name="street"]').attr('data-lon','');*/
					$('#modal-delivery').modal();
				});

				/*$('#modal-delivery').on($.modal.BEFORE_CLOSE, function(event, modal) {
					//return false;
					if($('#tabs [aria-expanded="true"] .button.select_delivery').hasClass('disabled'))
						return false;

					if($('input#addnewaddress:checked').length >0 ) {
						//e.preventDefault();

						$('input[name="address"]').removeAttr( "checked" );
						let data = [];
						data.street = $('.newaddressform [name="street"]').val();
						data.house = $('.newaddressform [name="house"]').val();
						data.floar = $('.newaddressform [name="floar"]').val();
						data.flat = $('.newaddressform [name="flat"]').val();
						data.entrance = $('.newaddressform [name="entrance"]').val();
						data.comment = $('.newaddressform [name="comment"]').val();
						data.lon = $('[name="new-lon"]').val();
						data.lat = $('[name="new-lat"]').val();
						let deliveryType = 'delivery';

						if(data.street.length == 0){
							$('.newaddressform [name="street"]').addClass('has-error');
							return;
						}

						$.modal.close();
						$('span.delivery-type').html('Доставка');
						$('span.addressline').html(data.street);

						$('input[name="street"]').val(data.street);
						$('input[name="house"]').val(data.house);
						$('input[name="lat"]').val(data.lat);
						$('input[name="lon"]').val(data.lon);
						$('input[name="delivery-type"]').val(deliveryType);
					}
					else {
						//e.preventDefault();

						$.modal.close();
						let data = methods.formToArray('form#create-order');

						let form = $('#modal-delivery #tabs [aria-expanded="true"] form');//e.target.closest('form');
						let address = $(form).find('[name="address"]:checked');
						let addressline = address.data('address');
						let deliveryType = address.data('type');
						$('span.delivery-type').html(deliveryType);
						$('span.addressline').html(addressline);

						$('input[name="street"]').val(address.data('street'));
						$('input[name="house"]').val(address.data('house'));
						$('input[name="lat"]').val(address.data('lat'));
						$('input[name="lon"]').val(address.data('lon'));
						$('input[name="delivery-type"]').val(address.data('method'));
					}
				});*/
				/**
				 * Нажимаем подтвердить адрес
				 */
				$(document).on("click", '.close-modal2', function (e) {
					$.modal.close();
				});

				$(document).on("click", '.select_delivery ', function (e) {
					if($('#tabs [aria-expanded="true"] .button.select_delivery').hasClass('disabled')) {
						//$.modal.close();
						return false;
					}


					if($('#modal-delivery #tabs [aria-expanded="true"] input#addnewaddress:checked').length >0 ) {
						e.preventDefault();
						//console.log('Нажимаем подтвердить адрес - новый адрес');
						//$('input[name="address"]').removeAttr( "checked" );
						let data = [];
						data.street = $('.newaddressform [name="street_line"]').val();
						data.house = $('.newaddressform [name="house"]').val();
						data.floar = $('.newaddressform [name="floar"]').val();
						data.flat = $('.newaddressform [name="flat"]').val();
						data.entrance = $('.newaddressform [name="entrance"]').val();
						data.comment = $('.newaddressform [name="comment"]').val();
						data.lon = $('[name="new-lon"]').val();
						data.lat = $('[name="new-lat"]').val();
						let deliveryType = 'delivery';

						if(data.street.length == 0){
							$('.newaddressform [name="street"]').addClass('has-error');
							return;
						}
						else {
							$('.newaddressform [name="street"]').removeClass('has-error');
						}

						$.modal.close();
						$('span.delivery-type').html('Доставка');
						//console.log(data);

						let newstreet = data.street;
						if(data.entrance.length)
							newstreet += ' п. '+data.entrance;
						if(data.flat.length)
							newstreet += ', кв. '+data.flat;

							newstreet += '<br/>'+$('input[name="comment"]').val();

						$('span.addressline').html(newstreet);

						$('input[name="street"]').val(data.street);
						$('input[name="house"]').val(data.house);
						$('input[name="floar"]').val(data.floar);
						$('input[name="porch"]').val(data.entrance);
						$('input[name="flat"]').val(data.flat);
						$('input[name="lat"]').val(data.lat);
						$('input[name="lon"]').val(data.lon);
						$('input[name="delivery-type"]').val(deliveryType);
					}
					else {
						e.preventDefault();
						//console.log('Нажимаем подтвердить адрес - существующий');
						$.modal.close();
						let data = methods.formToArray('form#create-order');

						let form = $('#modal-delivery #tabs [aria-expanded="true"] form');//e.target.closest('form');
						let address = $(form).find('[name="address"]:checked');
						let addressline = address.data('address');
						let deliveryType = address.data('type');
						$('span.delivery-type').html(deliveryType);
						$('span.addressline').html(addressline);

						let prefix = '#modal-delivery #tabs [aria-expanded="true"] form ';
						//console.log($(prefix + 'input[name="street"]'));
						$('input[name="street"]').val(address.data('street'));
						$('input[name="house"]').val(address.data('house'));
						$('input[name="lat"]').val(address.data('lat'));
						$('input[name="floar"]').val(data.floar);
						$('input[name="flat"]').val(data.flat);
						$('input[name="lon"]').val(address.data('lon'));
						$('input[name="delivery-type"]').val(address.data('method'));
					}
					$('input[name="comment"]').val($('#comment').val());
					$( ".order-step2" ).trigger( "click" );
				});

				/**
				 * Переключение получателя заказать себе / заказать другу
				 */
				$(document).on("click", 'input[name="delivery-for"]', function (e) {
					let deliveryFor = $(this).val();

					$('.delivery-for').hide();
					$('.' + deliveryFor).show();
				});
				/**
				 * Переход из корзина на шаг оформления заказа
				 */
				$(document).on("click", '.order-step1', function (e) {
					e.preventDefault();

					methods.ajaxRequest('getHtml', '', 'template').then(function (response) {
						if (response.status === 'success') {
							wrap.html(response.data.html);
							showInBasket(methods.getCart());
							methods.setCurStep(0);
							let cartItems = methods.getCart();
							let totalCost = getCostNew(cartItems);

							$('.cart__summary-sum span').html(totalCost);
						}
					});
				});


				/**
				 * Обработчик выбора адреса из выпадающего списка
				 */
				document.addEventListener('enterAdress', function (e) {

					let data = [];
					data.city = e.detail.city;
					data.street = e.detail.street;
					data.house = e.detail.house;
					data.lat = e.detail.lat;
					data.lon = e.detail.lon;
					data.deliverytype = e.detail.method;
					//console.log(data.deliverytype);
					if(data.deliverytype == 'delivery') {
						$('#tabs').addClass('bisy');
						methods.ajaxRequest('calculateDeliveryCost', data, '').then(function (response) {
							if (response.status === 'success') {
								let validate = methods.validateArea(response.data);

								let totalCost = $('.cart__summary-sum span').html();

								$('.order__info-delivery-price span').html(response.data.cost);
								$('.order__info-delivery-price-total span').html(Number(totalCost) + Number(response.data.cost));
								$('input[name="ordercost"]').val(Number(response.data.cost));
								$('#tabs').removeClass('bisy');
							}
						});
					}
					else {
						let totalCost = $('.cart__summary-sum span').html();
						$('.order__info-delivery-price span').html('0');
						$('.order__info-delivery-price-total span').html(Number(totalCost));
						$('input[name="ordercost"]').val('0');
					}

				}, false);

				document.addEventListener('enterAdress_OLD', function (e) {
					console.log('enterAddressOld');
					$('#tabs').addClass('bisy');
					let data = [];

					data.city = e.detail.address.city;
					data.street = e.detail.address.street;
					data.house = e.detail.address.house;
					data.lat = e.detail.address.location[0];
					data.lon = e.detail.address.location[1];
					data.method = e.detail.delivery

					$('input[name="new-lat"]').val(data.lat);
					$('input[name="new-lon"]').val(data.lon);

					data.deliverytype = 'delivery';//$('input[name="delivery-type"]:checked').val();

					if(e.detail.address.street.length>0)
						$('input[name="street_line"]').val(e.detail.address.street + ' ' + e.detail.address.house);
					else
						$('input[name="street"]').val(e.detail.address.rawData);

					$('input#street').val(e.detail.address.street);
					$('input[name="house"]').val(e.detail.address.house);

					$('input[name="house"]').val(e.detail.address.house);


					methods.ajaxRequest('calculateDeliveryCost', data, '').then(function (response) {
						if (response.status === 'success') {
							/*let totalCost = $('.cart__summary-sum span').html();
							$('.order__info-delivery-price span').html(response.data);
							$('.order__info-delivery-price-total span').html(Number(totalCost) + Number(response.data));*/
							let validate = methods.validateArea(response.data);

							let totalCost = $('.cart__summary-sum span').html();

							$('.order__info-delivery-price span').html(response.data.cost);
							$('.order__info-delivery-price-total span').html(Number(totalCost) + Number(response.data.cost));
							$('input[name="ordercost"]').val(Number(response.data.cost));
							$('#tabs').removeClass('bisy');
						}
					});
				}, false);

				/**
				 * Обработчик выбора времени доставки
				 */
				$(document).on("click", 'input[name="delivery-time"]', function (e) {
					if ($(this).is(':checked') && $(this).hasClass('pick_time')) {
						methods.datePicker('input[name="pick_time"]');

						$('.pick_time_block').show();
					} else {
						$('.pick_time_block').hide();
					}
				});

				/**
				 * Попытка к переходу на шаг 2
				 */
				$(document).on("click", '.order-step2', function (e) {
					console.log('go to step2');
					e.preventDefault();

					if (/*checkLogin()*/ $(this).hasClass('needauth')) {
						e.stopPropagation();
						e.preventDefault();
						this.modal = $('#modal-login').modal();
						return;
					}

					let cart = methods.getCart();

					if (cart.length == 0) {
						console.log('cart null');
						let url = window.location.href;
						url += '?delivery=1';
						window.location.href = url;
						//document.location.reload();
						return;
					}
					let data = methods.formToArray('form#cartform');

					if(data['delivery-type'].length > 0 && data['lat'].length > 0 && data['lon'].length > 0 && data['street'].length > 0) {
						methods.ajaxRequest('getHtml', data, 'step2').then(function (response) {

							if (response.status === 'success') {
								let cartItems = methods.getCart();
								wrap.html(response.data.html);
								showInBasket(cartItems);

								let totalCost = getCostNew(cartItems);
								$('.cart__summary-sum span').html(totalCost);
								methods.setCurStep(1);
								$('input[name="cart"]').val(JSON.stringify(cartItems));
								$('.mask_phone').inputmask("+9 (999) 999-99-99");

								//methods.displayDeliveryModal();
							}
						});
					}
					else {
						methods.displayDeliveryModal();
					}
				});
				$(document).on("click", '.order-step3', function (e) {
					e.preventDefault();

					methods.getCart()

					methods.ajaxRequest('getHtml', '', 'step3').then(function (response) {
						if (response.status === 'success') {
							wrap.html(response.data.html);
							let cartItems = methods.getCart();
							methods.setCurStep(2);
							$('input[name="cart"]').val(JSON.stringify(cartItems));
						}
					});
				});

				/**
				 * Нажимаем оформить заказ
				 */
				$(document).on("click", '.create-order', function (e) {

					if ($('.order__inner').hasClass('bisy')) {
						return;
					}

					$('.order__inner').addClass('bisy');
					let data = methods.formToArray('form#create-order');
					methods.makeOrder(data);
				});
			},
			selectAddress: function () {

			},

			displayDeliveryModal: function () {
				if($('#modal-delivery:visible').length == 0) {

				}
				var selectionFired = new CustomEvent("build", {
					"detail": $params.taxiorder
				});
				$('input[name="street"]').val('');
				$('input[name="street"]').attr('data-city','');
				$('input[name="street"]').attr('data-lat','');
				$('input[name="street"]').attr('data-lon','');
				$('#modal-delivery').modal();
				$( 'input[name="address"]:radio:first' ).click();

				$("#modal-delivery").tabs();
				document.dispatchEvent(selectionFired);
			},
			validateArea: function (data) {

				if(data.avail == false) {
					$('.order__delivery-select-price').hide();
					$('#tabs-1 .button.select_delivery').addClass('disabled');
					$('.address-error').show();
					$('.address-error.taberror').html('Район не поддерживается. Выберите другой адрес или воспользуйтесь самовывозом.');
				}
				if(data.min.need !== true) {
					$('.order__delivery-select-price').hide();
					$('#tabs-1 .button.select_delivery').addClass('disabled');
					$('.address-error').show();
					$('.address-error').html('Для доставки закажите еще на '+data.min.need+' ₽ или выберите самовывоз');
				}
				if(data.avail !== false && data.min.need == true) {
					$('#tabs-1 .button.select_delivery').removeClass('disabled');
					$('.order__delivery-select-price').show();
					$('.address-error').hide();
				}
			},

			makeOrder: function (data) {

				methods.ajaxRequest('getHtml', data, 'step3').then(function (response) {
					if (response.status === 'success') {
						$('.order__inner').removeClass('bisy');
						wrap.html(response.data.html);
						let cartItems = methods.getCart();

						showInBasket(cartItems);

						var selectionFired = new CustomEvent("build", {
							"detail": $params.taxiorder
						});

						document.dispatchEvent(selectionFired);

						let totalCost = getCostNew(cartItems);
						$('.cart__summary-sum span').html(totalCost);
						$('input[name="cart"]').val(JSON.stringify(cartItems));
						if($('.mask_phone').length > 0)
							$('.mask_phone').inputmask("+9 (999) 999-99-99");
						if (response.data.step == 'step3') {
							methods.setCurStep(2);
							localStorage.removeItem('items');
							methods.getOrderStatus();
							methods.updateStatus(response.data.orderid);
						}
						else {
							let totalCost = $('.cart__summary-sum span').html();
							let orderCost = $('input[name="ordercost"]').val();

							$('.order__info-delivery-price span').html(orderCost);
							$('.order__info-delivery-price-total span').html(Number(totalCost) + Number(orderCost));

						}
					}
				});
			},
			getOrderStatus: function () {
				methods.ajaxRequest('getOrderStatus', $('span.orderstatus').data('orderid'), '').then(function (response) {
					if (response.status === 'success') {
						$('span.orderstatus').html(response.data.result);
						methods.updateStatus();
					}
				});
			},
			updateStatus: function () {

				let interval = setInterval(function() {
					methods.getOrderStatus();
				}, 30000);

				setTimeout(function() {
					clearInterval(interval);
				}, 33000);
				//methods.getOrderStatus();
			},
			getDateAttsFromEl: function (e) {
				let data = [];

				data.city = $(e).data('city');
				data.house = $(e).data('house');
				data.street = $(e).data('street');
				data.lat = $(e).data('lat');
				data.lon = $(e).data('lon');
				data.method = $(e).data('method');

				var enterAdress = new CustomEvent("enterAdress", {
					"detail": data
				});

				document.dispatchEvent(enterAdress);
			},
			datePicker: function (selector) {
				// Зададим стартовую дату
				var start = new Date(),
					prevDay,
					startHours = 9;

				// 09:00
				start.setHours(9);
				start.setMinutes(0);

				var datatoday = new Date();
				var datatodays = new Date(Date.now() + 1 * 24 * 60 * 60 * 1000);
				;

				/*var date = new Date()
				let oneDayFromNow = new Date();
				oneDayFromNow.setFullYear(oneDayFromNow.getFullYear(), oneDayFromNow.getMonth(), 18 + 1);*/

				$(selector).datepicker({
					timepicker: true,
					startDate: start,
					minDate: datatoday,
					maxDate: datatodays,
					minHours: startHours,
					//maxHours: 18,
					onSelect: function (fd, d, picker) {
						// Ничего не делаем если выделение было снято
						if (!d) {
							return;
						}

						var day = d.getDay();

						// Обновляем состояние календаря только если была изменена дата
						if (prevDay != undefined && prevDay == day) {
							return;
						}
						prevDay = day;
					}
				});
			},

			validate: function () {
			},

			setCurStep: function (step) {
				let prevstep = step-1;
				$('.js-header-step').removeClass('active');
				for (var i = 0; i <= prevstep; i++) {
					$('[data-step="' + prevstep + '"] i span').hide();
					$('[data-step="' + prevstep + '"] img').show();
				}

				$('[data-step="' + step + '"]').addClass('active');
			},

			formToArray: function (form) {
				return $(form).serializeArray().reduce(function (obj, item) {
					obj[item.name] = item.value;
					return obj;
				}, {});
			},

			ajaxRequest: function (method, data, template = false) {
				return BX.ajax.runComponentAction(
					params.component,
					method,
					{
						mode: 'class',
						dataType: 'json',
						data: {'data': data, 'template': template},
					}
				)
			},

			reset: function () {
			},

			getCart: function () {
				return basket.retrieve();

				if (currentItems.length > 0) {
					this.cartItems = currentItems;
				} else {
					this.cartItems = false;
				}
			}
		};

		methods.init();
	}
}