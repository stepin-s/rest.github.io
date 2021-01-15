function Profile($params) {
	const params = $params;
	const wrap = $('.profile');

	this.cartItems = false;

	if (wrap.length) {

		const methods = {
			init: function () {
				$(document).on("click", '.' + params.editProfileField, $.proxy(methods.editProfile, this));
				$(document).on("click", '.' + params.addressEditBtn, $.proxy(methods.showForm, this));
				$(document).on("click", '.saveAddress', $.proxy(methods.updateAddress, this));
				$(document).on("click", '.' + params.addressAddBtn, $.proxy(methods.showFormAdd, this));
				document.addEventListener('enterAdress_OLD', function (e) {
					let data = [];
					data.city = e.detail.address.city;
					data.street = e.detail.address.street;
					data.house = e.detail.address.house;
					data.lat = e.detail.address.location[0];
					data.lon = e.detail.address.location[1];
					data.method = e.detail.delivery

					//console.log(data.lat);
					$('input[name="lat"]').val(data.lat);
					$('input[name="lon"]').val(data.lon);

					let streetLine = e.detail.label;

					if(e.detail.address.street.length > 0) {
						streetLine = e.detail.address.street + ' ' + e.detail.address.house;
						console.log(e.detail.address.street);
					}

					$('input[name="street"]').val(streetLine);
					$('input[name="house"]').val(e.detail.address.house);

				}, false);

			},

			updateAddress: function (e) {
				let data = methods.formToArray('.profile-edit form');

				methods.ajaxRequest('updateAddress', data, 'edit').then(function (response) {
					if (response.status === 'success') {
						$.modal.close();
						document.location.reload()
					}
				});
			},

			editProfile: function (e) {
				if($(e.target).hasClass('editable-on')) {
					$(e.target).attr("readonly", true);
					$(e.target).removeClass('editable-on');
					methods.saveProfile();
				}
				else {
					$(e.target).attr("readonly", false);
					$(e.target).addClass('editable-on');
				}
			},

			saveProfile: function () {
				let data = methods.formToArray('.' + params.formClass);

				methods.ajaxRequest('saveProfile', data, '').then(function (response) {
					if (response.status === 'success') {
						//err - info -> code5
						console.log(response);
					}
				});
				console.log(data);
			},

			showForm: function (e) {
				let data = [];
				data.id = $(e.target).data('id');
				console.log(data);
				e.stopPropagation();
				e.preventDefault();

				methods.ajaxRequest('getHtml', data, 'edit').then(function (response) {
					if (response.status === 'success') {
						$('#' + params.modalShell).html(response.data.html);
						var selectionFired = new CustomEvent("build", {
							"detail": $params.taxiorder
						});
						document.dispatchEvent(selectionFired);
						$('#' + params.modalShell).modal();
					}
				});
			},

			showFormAdd: function (e) {
				let data = [];
				data.id = $(e.target).data('id');
				console.log(data);
				e.stopPropagation();
				e.preventDefault();

				methods.ajaxRequest('getHtml', data, 'add').then(function (response) {
					if (response.status === 'success') {
						$('#' + params.modalShell).html(response.data.html);
						//console.log($params);
						var selectionFired = new CustomEvent("build", {
							"detail": $params.taxiorder
						});
						document.dispatchEvent(selectionFired);
						$('#' + params.modalShell).modal();
					}
				});
			},

			validate: function () {
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
			formToArray: function (form) {
				return $(form).serializeArray().reduce(function (obj, item) {
					obj[item.name] = item.value;
					return obj;
				}, {});
			},
		};

		methods.init();
	}
}