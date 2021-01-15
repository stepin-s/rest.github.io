taxiOrder.ready();

if ($('.mask_phone').length) {
	$('.mask_phone').inputmask("+9 (999) 999-99-99");
	if(!$('.mask_phone').hasClass('no7'))
		$('.mask_phone').val('+7');
}
if ($('#login-code').length) {
	$('#login-code').inputmask("999999");
}

$('body').on('click', '.user-menu', function () {
	$('.user-menu__wrapper').toggleClass('opened');
})

$(document).click(function (event) {
	if (!$(event.target).closest(".user-menu, .user-menu__wrapper").length) {
		$(".user-menu__wrapper").removeClass('opened');
	}
});

///

///////////////
//shopping cart//
//////////////

$('body').on('click', '.js-add', function (e) {
	e.preventDefault;

	let item = $(this).parents('.js-item');
	//console.log('js-add');
	addToBasket(item);
})

$('body').on('click', '.js-remove', function (e) {
	e.preventDefault;
	let item = $(this).parents('.js-item');
	removeFromBasket(item);
})

$('body').on('click', '.js-basket-remove', function (e) {
	e.preventDefault;
	let item = $(this).parents('.js-item');
	decrementItem(item);
})

$('body').on('click', '.js-basket-add', function (e) {
	e.preventDefault;
	let item = $(this).parents('.js-item');
	//console.log('js-add');
	incrementItem(item);
})

$('body').on('click', '.js-basket-delete-link', function () {
	let reallyDelete = confirm("Вы уверены, что хотите удалить товар из корзины?");
	let item = $(this).parents('.js-item');

	if (reallyDelete) {
		deleteItem(item);
	}
})

// $('body').on('click', '.js-basket-delete', function() {
//     let item = $(this).parents('.js-item');
//     deleteItem(item);
// })


$('body').on('click', '.modal-card__settings-item-left', function () {
	let parent = $(this).parents('.js-item');
	getCost(parent);

	if ($(this).parents('.js-types').length) {
		let type = $(this).find('.styled-radio').attr('data-id');
		$(parent).find('.js-control-type').hide();
		$(parent).find('.js-control-type[data-type=' + type + ']').show();
	}
})


function getCost(parent) {
	let id = $(parent).attr('data-id');

	let item = $('.js-item[data-id=' + id + ']');
	//let itemQuantity = $(item).find('.quantity')[0] ? $(item).find('.quantity')[0].innerText : '1';

	$(item).each(function () {
		let priceTotal = $(this).find('.js-item-cost span')[0],
			itemTypeCost = $(this).find('.js-types .styled-radio:checked').attr('data-cost'),
			itemType = $(this).find('.js-types .styled-radio:checked').attr('data-id'),
			itemDops = $(this).find('.js-dops .styled-checkbox:checked'),
			itemDopCost = 0;

		itemDops.each(function () {
			itemDopCost += +$(this).attr('data-cost');
		})

		if (!($(this).find('.js-types').hasClass('has-options')) && !($(this).find('.js-dops').hasClass('has-options'))) {
			let itemQuantity = $(item).find('.js-control-type[data-type=' + itemType + ']').find('.quantity')[0].innerText;
			priceTotal.innerText = (+itemTypeCost + itemDopCost) * (+itemQuantity);
		}
	})
}

function getCostNew(items) {
	let totalCost = 0;

	$(items).each(function (index, value) {
		totalCost += Number(value.type.cost) * Number(value.params.quantity);
		$(value.dops).each(function (ind, val) {
			totalCost += Number(val.cost) * Number(value.params.quantity);
		});
	});
//alert(totalCost);
	return totalCost;
}

let basket = {
	assemble: function (item) {
		//вернуть инфо по текущему товару

		if ($('.goods').length) {
			let params = {
				id: $(item).attr('data-id'),
				name: $(item).find('.js-item-name')[0].innerText,
				quantity: $(item).find('[data-type=' + $(item).find(".js-types .styled-radio:checked").attr("data-id") + '] .js-controls').attr('data-quantity'),
				img: $(item).find('.js-item-img').attr('data-img'),
				badge: $(item).find('.js-item-badge').attr('data-badge'),
				cost: $(item).find('.js-item-cost span')[0].innerText
			}


			let type = {
				id: $(item).find('.js-types .styled-radio:checked').attr('data-id'),
				name: $(item).find('.js-types .styled-radio:checked').attr('data-name'),
				description: $(item).find('.js-types .styled-radio:checked').attr('data-description'),
				cost: $(item).find('.js-types .styled-radio:checked').attr('data-cost')
			}


			let dops = [];
			$(item).find('.js-dops .styled-checkbox:checked').each(function () {
				dops.push({
					id: $(this).attr('data-id'),
					name: $(this).attr('data-name'),
					cost: $(this).attr('data-cost')
				});
			})


			let total = {
				params,
				type,
				dops
			}

			return (total);

		} else {
			let params = {
				id: $(item).attr('data-id'),
				name: $(item).find('.cart__item-name')[0].innerText,
				cost: $(item).find('.cart__item-price span')[0].innerText,
				quantity: $(item).find('.quantity')[0].innerText,
				img: $(item).find('.cart__item-img').attr('data-img'),
				badge: $(item).find('.badge').attr('data-badge')
			}


			let type = {
				id: $(item).find('.cart__item-option').attr('data-id'),
				name: $(item).find('.cart__item-option').attr('data-name'),
				description: $(item).find('.cart__item-option').attr('data-description'),
				cost: $(item).find('.cart__item-option').attr('data-cost')
			}


			let dops = [];
			$(item).find('.cart__item-wishes li').each(function () {
				dops.push({
					id: $(this).attr('data-id'),
					name: $(this).attr('data-name'),
					cost: $(this).attr('data-cost')
				});
			})

			let total = {
				params,
				type,
				dops
			}

			return (total);
		}
	},
	retrieve: function () {
		//вернуть все товары из локал сторэдж
		let items = [];
		if (localStorage.getItem('items')) {
			items = JSON.parse(localStorage.getItem('items'));
		}
		/*let items = [];
		let cart = [];*/
		// if(this.getCookie('items')){

		//items = JSON.parse(this.getCookie('items'));
		// console.log(this.getCookie('items'));
		//console.log(items);
		//}
		/*this.ajaxSubmit('gootax:order', 'getCookie', 'items').then(function (response) {
			if (response.status === 'success') {
				console.log(JSON.parse(response.data));
				return JSON.parse(response.data);
			}
		});*/
		// console.log(items)
		// items = items.sort(function (a, b) {
		// 	return a.params.id > b.params.id;
		// });


		items.sort(function(a,b){
			var _a = a.params.name.trim().toLowerCase();
			var _b = b.params.name.trim().toLowerCase();
			return _a .localeCompare(_b) ;
		})
	

		return (items);
	
	},
	add: function (items, newItem) {
		//записать новый массив товаров в локал сторэдж
		let exp = new Date((new Date).getTime() + 3600 * 24 * 7 * 1000);
		items.push(newItem);
		items.sort(function(a,b){
			var _a = a.params.name.trim().toLowerCase();
			var _b = b.params.name.trim().toLowerCase();
			return _a .localeCompare(_b) ;
		})
		localStorage.setItem('items', JSON.stringify(items));

		let data = [];
		data.items = JSON.stringify(items);

		this.setCookie(data);
		//document.cookie = 'items=' + JSON.stringify(items) + ';path=/;expires=' + exp.toUTCString();
	},
	getCookie: function (name) {
		// var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
		// if (match)
		//     return match[2];
		this.ajaxSubmit('gootax:order', 'getCookie', name).then(function (response) {
			if (response.status === 'success') {

				return response;
			}
		});
	},
	setCookie: function (data) {
		//console.log('setCookie');

		this.ajaxSubmit('gootax:order', 'storeCookie', data).then(function (response) {
			if (response.status === 'success') {

			}
		});
	},

	ajaxSubmit: function (component, method, data) {
		return BX.ajax.runComponentAction(
			component,
			method,
			{
				mode: 'class',
				dataType: 'json',
				data: {'data': data},
			}
		)
	},
	buttonUpdate: function (items) {
		//проставить количество товаров в кнопку корзины
		let $i = 0;
		items.forEach(element => {
			$i += +element.params.quantity;
		});

		if ($('.button-cart__number').length) {
			$('.button-cart__number')[0].innerText = $i;
		}
	},
	delete: function (items, id, type, notstore = false) {
		//записать массив текущих товаров в локал сторэдж без товара id и type id
		let exp = new Date((new Date).getTime() + 3600 * 24 * 7 * 1000);

		let jsonItems = JSON.stringify(items.filter(item => {
			return item.params.id !== id || item.type.id !== type;
		}));

		items.sort(function(a,b){
			var _a = a.params.name.trim().toLowerCase();
			var _b = b.params.name.trim().toLowerCase();
			return _a .localeCompare(_b) ;
		})
		
		localStorage.setItem('items', JSON.stringify(items.filter(item => {
			return item.params.id !== id || item.type.id !== type;
		})));

		let data = [];
		data.items = JSON.stringify(items.filter(item => {
			return item.params.id !== id || item.type.id !== type;
		}));

		if(notstore === false)
		this.setCookie(data);
		// document.cookie = 'items=' + JSON.stringify(items.filter(item => {
		//    return item.params.id !== id || item.type.id !== type;
		// })) + ';path=/;expires=' + exp.toUTCString();
	}
}


function onLoad() {
	let currentItems = basket.retrieve();

	if (currentItems.length) {
		basket.buttonUpdate(currentItems);

		if ($('.goods').length) {
			//basket.buttonUpdate(currentItems);
			currentItems.forEach(el => {
				let id = el.params.id,
				quantity = el.params.quantity,
				type = el.type.id;

			let item = $('.js-item[data-id=' + id + ']');


			$(item).find('[data-type=' + type + '] .js-controls').attr('data-quantity', quantity);
			if ($(item).find('[data-type=' + type + '] .js-controls .quantity').length) {
				$(item).find('[data-type=' + type + '] .js-controls .quantity').each(function () {
					this.innerText = quantity;
				})
			}
			console.log(item)
			getCost(item);
			
			$(item).find('[data-type=' + type + '] .js-controls').toggle();
			$(item).find('[data-id=' + id + '] .js-controls').toggle();

		});
		} else if ($('.cart').length) {
			$('.cart').removeClass('empty');
			//showInBasket(currentItems);
			//getBasketCost();
		}

	} else if ($('.cart').length) {
		$('.cart').addClass('empty');
	}
	
}

onLoad();


function showInBasket(currentItems) {

	function sortfunction(a, b) {
		return a.params.id < b.params.id
	}

	// currentItems.sort(sortfunction)
	// currentItems.sort(function (x, y) {
	// 		return x.type.id - y.type.id;
	// 	})
	currentItems.forEach(el => {
		if(document.getElementById('template'))
		{
			el.type.qq = Number(el.params.quantity) * Number(el.type.cost);

			$(el.dops).each(function (ind, val) {
				el.type.qq += Number(val.cost) * Number(el.params.quantity);
			});
			let template = document.getElementById('template').innerHTML;
			let rendered = Mustache.render(template, el);
			$('.cart__items').append(rendered);
		}
	});
	checkMinCost();
}

function checkMinCost() {

	let currentItems = basket.retrieve();
	let curCost = Number(getCostNew(currentItems));
	let need = Number($('input[name="minorder"]').val());
	let diff = need - curCost;
	if(need>curCost && curCost > 0) {
		$('.minorder span').html('Для доставки закажите еще на '+ diff +'₽ или выберите самовывоз');
	}
	else {
		$('.minorder span').html('');
	}
}

function getBasketCost() {
	let cost = 0;
	let thisCost = 0;
	$('.js-item').each(function () {
		thisCost = 0;
		quantity = +($(this).find('.quantity')[0].innerHTML);
		cost += +($(this).find('.cart__item-option').attr('data-cost')) * (+quantity);
		thisCost += +($(this).find('.cart__item-option').attr('data-cost')) * (+quantity);
		if ($(this).find('.cart__item-wishes li').length) {
			$(this).find('.cart__item-wishes li').each(function () {
				cost += +($(this).attr('data-cost')) * (+quantity);
				thisCost += +($(this).attr('data-cost') * (+quantity));
			})
		}
		$(this).find('.cart__item-price span')[0].innerText = thisCost;
	})

	if ($('.cart__summary-sum span').length) {
		$('.cart__summary-sum span')[0].innerText = cost;
	}
}


function addToBasket(item) {
	let id = $(item).attr('data-id');
	let bothItem = $('.js-item[data-id=' + id + ']');
	let typeId = $(item).find(".js-types .styled-radio:checked").attr("data-id");

	let oldQuantity = $(bothItem).find('[data-type=' + typeId + '] .js-controls').attr('data-quantity');
	let newQuantity = +oldQuantity + 1;

	$(bothItem).find('[data-type=' + typeId + '] .js-controls').attr('data-quantity', newQuantity);
	$(bothItem).each(function () {
		if ($(this).find('[data-type=' + typeId + '] .js-controls .quantity').length) {
			$(this).find('[data-type=' + typeId + '] .js-controls .quantity')[0].innerText = newQuantity;
		}
	})

	if (newQuantity == '1') {
		$(bothItem).find('[data-type=' + typeId + '] .js-controls').toggle();
		$(bothItem).find('[data-id=' + id + '] .js-controls:first-child').hide();
		$(bothItem).find('[data-id=' + id + '] .js-controls:last-child').show();
	}

	let thisItem = basket.assemble(item);
	let currentItems = basket.retrieve();

	currentItems.forEach(element => {
		if((element.params.id === thisItem.params.id) && (element.type.id === thisItem.type.id))
		{
			basket.delete(currentItems, thisItem.params.id, thisItem.type.id, true);
		}
	});

	currentItems = basket.retrieve();
	//setTimeout(function(){
		basket.add(currentItems, thisItem);
	//}, 200);

	basket.buttonUpdate(currentItems);
	getCost(item);

}

function incrementItem(item) {
	let typeId = $(item).find(".cart__item-option").attr("data-id");

	let oldQuantity = $(item).find('.quantity')[0].innerText;
	let newQuantity = +oldQuantity + 1;

	$(item).find('.quantity')[0].innerText = newQuantity;

	let thisItem = basket.assemble(item);
	let currentItems = basket.retrieve();

	currentItems.forEach(element => {
		if((element.params.id === thisItem.params.id) && (element.type.id === thisItem.type.id))
		{
			basket.delete(currentItems, thisItem.params.id, thisItem.type.id, true);
			console.log("add")
		}
	});

	currentItems = basket.retrieve();

	basket.add(currentItems, thisItem);
	/*$('.js-item').remove();*/
	showInBasket(currentItems);
	getBasketCost();
}

function decrementItem(item) {
	let typeId = $(item).find(".cart__item-option").attr("data-id");
  
	let oldQuantity = $(item).find('.quantity')[0].innerText;
	let newQuantity = +oldQuantity - 1;
  
	$(item).find('.quantity')[0].innerText = newQuantity;
  
	let thisItem = basket.assemble(item);
	let currentItems = basket.retrieve();
  
	currentItems.forEach(element => {
	  if((element.params.id === thisItem.params.id) && (element.type.id === thisItem.type.id))
	  {
		basket.delete(currentItems, thisItem.params.id, thisItem.type.id, newQuantity !== 0);
	  }
	});
  
	currentItems = basket.retrieve();
  
	if (newQuantity != '0') {
	  basket.add(currentItems, thisItem);
	}
	if(newQuantity == '0') {
	  $('.js-item[data-id="'+thisItem.params.id+'"][data-type="'+thisItem.type.id+'"]').remove();
	  //console.log(thisItem);
	}
  
	//$('.js-item').remove();
	showInBasket(currentItems);
	getBasketCost();
  }

function deleteItem(item) {
	let thisItem = basket.assemble(item);
	let currentItems = basket.retrieve();

	currentItems.forEach(element => {
		if((element.params.id === thisItem.params.id) && (element.type.id === thisItem.type.id))
		{
			basket.delete(currentItems, thisItem.params.id, thisItem.type.id);
			console.log("delete")
		}
	});

	currentItems = basket.retrieve();

	$('.js-item[data-id="'+thisItem.params.id+'"]').remove();
		// console.log(thisItem);

	
	showInBasket(currentItems);
	getBasketCost();
}

function removeFromBasket(item) {

	let id = $(item).attr('data-id');
	let bothItem = $('.js-item[data-id=' + id + ']');
	let typeId = $(item).find(".js-types .styled-radio:checked").attr("data-id");

	let oldQuantity = $(bothItem).find('[data-type=' + typeId + '] .js-controls').attr('data-quantity');
	let newQuantity = +oldQuantity - 1;

	$(bothItem).find('[data-type=' + typeId + '] .js-controls').attr('data-quantity', newQuantity);
	$(bothItem).each(function () {
		if ($(this).find('[data-type=' + typeId + '] .js-controls .quantity').length) {
			$(this).find('[data-type=' + typeId + '] .js-controls .quantity')[0].innerText = newQuantity;
		}
	})

	if (newQuantity == '0') {
		$(bothItem).find('[data-type=' + typeId + '] .js-controls').toggle();
		// $(bothItem).find('[data-id=' + id + '] .js-controls').toggle();
	}

	let thisItem = basket.assemble(item);
	let currentItems = basket.retrieve();

	currentItems.forEach(element => {
		if((element.params.id === thisItem.params.id) && (element.type.id === thisItem.type.id))
		{
			basket.delete(currentItems, thisItem.params.id, thisItem.type.id);
		}
	});

	currentItems = basket.retrieve();

	if (newQuantity != '0') {
		basket.add(currentItems, thisItem);
	}

	basket.buttonUpdate(currentItems);
	getCost(item);

}

/////////
/*LOGIN*/
/////////
$('#modal-login').keydown(function (event) {
	//console.log('keydown');
	let keyPressed = event.keyCode || event.which;
	if (keyPressed === 13) {

		$('.login-step:visible a.button').trigger('click');
		event.preventDefault;
		return false;
		//$(this).closest('form').submit();
	}
});

$('body').on('click', '#send-code', function (e) {
	$('.login-step-1 .error').text('');
	e.preventDefault;
	let phone = $('.modal-row__phone input').inputmask('unmaskedvalue');
	if (phone.length > 10) {
		sendCode(phone);
	} else {
		$('.login-step-1 .error').text('Введите корректный номер телефона')

	}
})

$('body').on('click', '#login', function (e) {
	e.preventDefault;
	let phone = $('.modal-row__phone input').inputmask('unmaskedvalue');
	let code = $('.modal-row__code input').val();
	smsConfirm(phone, code);
})

function sendCode(phone) {
	taxiOrder.sendSms(phone, function (result) {
		if (result.success) {
			$('.login-step').toggle();
			$('.modal-row__code input').focus();
		}
	})
}

function smsConfirm(phone, code) {
	$('.login-step-2 .error').text('');

	taxiOrder.login(phone, code, function (result) {
		let exp = new Date((new Date).getTime() + 3600 * 24 * 7 * 1000);
		document.cookie = 'api_browser_key=' + result.browserKey + ';path=/;expires=' + exp.toUTCString();
		document.cookie = 'api_token=' + result.token + ';path=/;expires=' + exp.toUTCString();
		document.cookie = 'phone=' + phone + ';path=/;expires=' + exp.toUTCString();
		localStorage.setItem('phone', JSON.stringify(phone));

		//document.location.reload();
		if ($('.create-order').length) {
			$('.create-order').trigger('click');
		} else {
			//console.log('scripts');
			let url = window.location.href;
			url += '?delivery=1';
			//window.location.href = url;
			window.location.href = window.location.href.split('#')[0] + "?delivery";
			//document.location.reload();
		}
		$.modal.close();
	}, function (error) {
		$('.login-step-2 .error').text(error.text);
	})
}

function ajaxRequest(method, data, template = false) {
	return BX.ajax.runComponentAction(
		'gootax:order',
		method,
		{
			mode: 'class',
			dataType: 'json',
			data: {'data': data, 'template': template},
		}
	)
}

function checkLogin2() {

	result = ajaxRequest('checkLogin', 'a');

	return result;
}

function checkLogin() {

	/*ajaxRequest('checkLogin', 'a').then(function (response) {
		console.log(response);
		if (response.data === false) {

			this.taxiOrder.needSendSms(JSON.parse(localStorage.getItem('phone').length), function (need) {
				return need;
			});
		}
		else {
			return true;
		}
	});*/
	//(JSON.parse(localStorage.getItem('phone')));

	if (JSON.parse(localStorage.getItem('phone'))) {
		taxiOrder.needSendSms(JSON.parse(localStorage.getItem('phone').length), function (need) {
			return need;
		})
	}
	else {
		return true;
	}
}

$('.button-cart').click(function (e) {
	return;
	if (checkLogin()) {
		e.stopPropagation();
		e.preventDefault();
		$('#modal-login').modal();
	}
})


/////////
/*MOBILE APPS*/
/////////

$('.mobile-download__close').click(function () {
	$('.mobile-download').hide();
	let exp = new Date((new Date).getTime() + 3600 * 24 * 7 * 1000);
	document.cookie = 'show_mobile_apps=no;path=/;expires=' + exp.toUTCString();
});

/**
 Smooth scroll to anchor
 **/
/*$('a[href*=\\#]').on('click', function(event){
	event.preventDefault();
	$('html,body').animate({scrollTop:$(this.hash).offset().top - 100}, 500);
});*/

$('a').on('click', function (event) {
	if (typeof $(this).data('anchor') == 'undefined' || $($(this).data('anchor')).length == 0) {
		return;
	}

	// event.preventDefault();

	$('html,body').animate({scrollTop: $($(this).data('anchor')).offset().top - 100}, 500);
});