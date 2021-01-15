/* 
 * ������� �������� ������ �� �����
 */

/**
 * ������� �������� ������ �� ������� �����
 * @param {TaxiClient} client - ������� ���������� ����������� ��� ��������\��������� AJAX � ������ �������
 * @returns {TaxiOrderProcess}
 */
function TaxiOrderProcess(client) {
	if (!Object.create) {
		Object.create = (function() {
			function F() {
			}

			return function(o) {
				if (arguments.length != 1) {
					throw new Error('Object.create implementation only accepts one parameter.');
				}
				F.prototype = o;
				return new F()
			}
		})()
	}
	var self = Object.create({});
	/**
     * ���� ������������ ����� ������������ - � ���������\���������� �������� ��������
     */
	self._enabled = true;
	/**
     * ��������� ������
     */
	self.store = new TaxiDataStore();
	/**
     * ����� ��������������
     */
	self.components = {
		customCar: new TaxiCustomCarComponent(),
		route: new TaxiRouteComponent()
	};
	/**
     * ������ ��� ������� - ��������������
     */
	self.modalConfirm_useCustomCarInPriorOrder = '<p>��� ��������������� ������� ������ �������� ������ ��������������. ����� ������ �������������� �������������.</p>' +
	'<p>������ ����������� �������������. ��������� ���� ������ ����������� �� �����.</p>' +
	'<p>������� �����?</p>';
	/**
     * ��������� ��������� � ��������� �������
     */
	self.messages = {
		errors: {
			onOrderInfoFail: '! ��������� ������: �� ������� �������� ���������� � ������'
		}
	};
	/**
     * ��������, ��
     */
	self.timeouts = {
		/**
         * �������������
         */
		options: {
			/**
             * ���������� ����� ����� ��������� ��� �������� ������������ ��������,
             * ����� ��������� �������, �������� �������� ��������� � ������
             */
			regression: {
				/**
                 * ���� ������������ ���������
                 */
				enabled: true,
				/**
                 * ����������� ���������� 
                 */
				incresaseOnFailValue: 1.5,
				/**
                 * ����� ����������� ���� ����� ���������� ���������� ��������
                 */
				limit: 10000
			}
		},
		/**
         * ����� ������ ���������� ���������� �� ������, ����� ��������� �������� ������
         */
		beforeFirstGetOrderInfo: 9000
	};
	/**
     * ��������� ���������� ���������� �� ������
     */
	self.lastOrderInfo = null;
	/**
     * ��������� ���������� � ������, ����� ��� ��������
     * ����
     */
	self.intervalOrderInfoUpdating = 3000;
	/**
     * �������� ��� ���������� ��������� �����
     */
	self._lastIntervalUpdatingCars = self.intervalUpdatingCars = 20000;
	/**
     * ����� �������� (������������ ������)�
     */
	self.orderId = $.cookie('api_order_id');
	/**
     * ������������ ����� ������
     */
	self.customCarId = null;
	/**
     * ��� �������� ���������� ���������� ��������� ����� ��������� ������
     */
	self.lastCreateOrderValidationResult = self.store.get('lastCreateOrderValidationResult');
	/**
     * ���� - ��������� ������������� ������������� ������ ������
     * ��� ��������������� ������
     */
	self.useCustomCarInPriorOrder = false;
	/**
     * ������������ ����� ������ - ���������� � ��������� ������
     */
	self.customCarInfo = null;
	/**
     * ���������� ���� ���������� ������� ��� �������� �������
     */
	self._isLocked = false;
	/**
     * ���� ���������� ������� ������ - ��� �������������� ��������� ��������
     */
	self._updatingOrder = false;
	/**
     * ���� ��� �������������� ���������������� (���������� ������ �������� ���
     * ����)
     */
	self.needForceReAuth = false;
	/**
     * ���� - ������ ������ �������� ����� - 
     * ���� ������� ����� �� ������������
     */
	self.hideRejectLink = false;
	/**
     * ���� ! ��� �������
     */
	self._browserKey = null;
	/**
     * ����� ! ��� �������
     */
	self._token = null;
	/**
     * ������ ��������� ��������� ��������� ��� �������� ������
     */
	self._lastValidationResult = null;
	/**
     * ������� ����������� ������, ���� ��� ��������� �����
     */
	self.carId = null;
	/**
     * ������� ���� ����� ������������
     */
	self.orderIdLabel = null;
	/**
     * ������� ������� - ��������� �������� ����
     */
	self.phone = null;
	/**
     * ���� ��� ���� - ��� ���������� ���� ����� �������� � �.�.
     */
	self.smsCode = null;
	/**
     * ����� �������
     */
	self.fromLat = null;
	/**
     * ����� �������
     */
	self.fromLon = null;
	/**
     * ����� ����
     */
	self.toLat = null;
	/**
     * ����� ����
     */
	self.toLon = null;
	/**
     * ��������� ��� �����-���� ������ 
     * @param {string} message ��������� �� ������
     * @param {object} errorInfo ���������� �� ������
     * @returns {Boolean}
     */
	self.onError = function(message, errorInfo) {
		alert(message);
	};
	/**
     * ��������� - �������� ������ �� ��������� �����:
     * �.�. ��� ��������� ������?
     * @returns {Boolean}
     */
	self.isOnFinalState = function() {
		return self.carId > 0;
	};
	/**
     * ����� ������ � ������� ������
     * @returns boolean
     */
	self.getFromPoint = function() {
		if (self.fromLat > 0 && self.fromLon > 0) {
			var point = [self.fromLat, self.fromLon];
			return point;
		} else {
			return false;
		}
	};
	/**
     * ����� ���� � ������� ������
     * @returns boolean
     */
	self.getToPoint = function() {
		if (self.toLat > 0 && self.toLon > 0) {
			return [self.toLat, self.toLon];
		} else {
			return false;
		}
	};
	/**
     * ������������� �������� ����� ����� ���������, ��������� ����������
     * @returns {undefined}
     */
	self.animateBusy = function() {

		$('.loader').css('display', 'block');
	};
	/**
     * ������������� �������� ����� ����� ���������, ��������� ����������
     * @returns {undefined}
     */
	self.animateUnbusy = function() {
		$('.loader').css('display', 'none');
	};
	/**
     * ������������� �������� �������
     * @returns {undefined}
     */
	self.lock = function() {
		self.animateBusy();
		self._isLocked = true;
		// ���� �������������� ������������� ����� 30 ��� ����� ����������
		setTimeout(function() {
			self.unlock();
		}, 30000);
	};
	/**
     * �������������� �������� ��������
     * @returns {undefined}
     */
	self.unlock = function() {
		self.animateUnbusy();
		self._isLocked = false;
	};
	/**
     * ��������� ���������� ��������
     * @returns {undefined}
     */
	self.isLocked = function() {
		return self._isLocked;
	};
	/**
     * ������� ���������� ����������� ��� ��������\��������� AJAX � ������ �������
     * @type {TaxiClient}
     */
	if (!client) {
		/**
         * @type TaxiClient
         */
		self.client = new TaxiClient();
	} else {
		/**
         * @type TaxiClient
         */
		self.client = client;
	}

	/**
     * ��������� �������, ���� �������� ���������, ���� ��� �������� - �� ������ 
     * ����� ���������� ������ ��������
     * @param {integer} oldValue - ������ ��������, ��
     * @returns {integer} - ����� (����������� �������)
     */
	self.applyTimeoutsRegression = function(oldValue, callback)
	{
		var newValue = oldValue;
		var options = self.timeouts.options.regression;
		if (options.enabled) {
			newValue = Math.round(options.incresaseOnFailValue * oldValue);
			if (newValue > options.limit) {
				newValue = oldValue;
			}
			console.log('��������� ��������� �� ' + oldValue + ' �� ' + newValue);
		}
		if (typeof (callback) === 'function') {
			callback(newValue);
		}
		return newValue;
	};
	/**
     * ��������� ������ �� �������
     * @param {string} methodName - ����� ��� ������ �������
     * @param {object} params - ���������
     * @param {function} onSuccess - ��� �������� ����������
     * @param {function} onFail - (�����������) - ��� ������� �������
     * @returns {undefined}
     */
	self.sendToClient = function(methodName, params, onSuccess, onFail) {
		if (!self._enabled) {
			console.log('Not send to client, because TaxiOrderProcess is not enabled');
			return false;
		}
		var method = new TaxiMethod(methodName);
		method.successCallback = onSuccess;
		method.errorCallback = onFail || function() {
			console.log('������ ��� �������� �������!');
		};
		method.params = params;
		self.client.executeQuery(method);
	};
	/**
     * �������� �����������, ������ ��� ���� � �.�.
     * @param {object} validationResult - ��������� ������� ���������
     * @returns {undefined}
     */
	self.checkSmsAuthorization = function(validationResult) {
		console.log('Checking SMS authorization');
		self.phone = validationResult.paramsToValidate.phone;
		self.sendToClient('sendSms', {
			phone: self.phone
		}, function(sendSmsResult) {
			console.log(sendSmsResult);
			if (sendSmsResult.resultCode > 0) {
				alert('�������� ��� -- ' + sendSmsResult.resultCode);
			}
			/*
             * ���� ��� �������������, �� ������� ����������� � ������������:
             *      - ������� ����� ������� �����, ��� ���� ��� ��������
             *      ���� ������ �������� �����������
             * ���, ����� 
             *      - ���������� ����� ����� ������� �����������
             */
			if (sendSmsResult.isAuthorizedNow) {
				self.createOrder(validationResult);
			} else {
				console.log('showEnterSmsCodeWindow');
				self.showEnterSmsCodeWindow();
			}
		});
	};
	/**
     * ! ��� ����
     * �������� �����������, ������ ��� ���� � �.�.
     * @param {function} onSuccess - ��������� ������� ���������
     * @returns {undefined}
     */
	self.forceSmsAuthorization = function(onSuccess) {
		if (!self.phone) {
			alert('���������� �������� ���������� � ������, ��� ��� ���������� �����������!');
			self.phone = prompt("������� ��� ������� �����:");
		}
		if (!self.phone) {
			return;
		}
		self.phone;
		self.sendToClient('sendSms', {
			phone: self.phone
		}, function(sendSmsResult) {
			console.log(sendSmsResult);
			/*
             * ���� ��� �������������, �� ������� ����������� � ������������:
             *      - ������� ����� ������� �����, ��� ���� ��� ��������
             *      ���� ������ �������� �����������
             * ���, ����� 
             *      - ���������� ����� ����� ������� �����������
             */
			if (sendSmsResult.isAuthorizedNow) {
				onSuccess();
			} else {
				self.showEnterSmsCodeWindow();
			}
		});
	};
	/**
     * �������� ������ �� ���������� ���������� ���������
     * @returns {undefined}
     */
	self.createLastOrder = function() {
		if (self._lastValidationResult) {
			self.createOrder(self._lastValidationResult);
		}
	};
	/**
     * �������� � ���� ����� ���������� ������
     * @param {type} orderId
     * @returns {undefined}
     */
	self._afterCreateOrder = function(orderId)
	{
		self._saveCookie('api_order_id', orderId);
		self._saveCookie('api_order_custom_car_id', self.customCarId);
		self._saveCookie('api_order_custom_car_info', self.customCarInfo);
		console.log('�������� � ���� ����� ������ ' + orderId.toString());
	};
	/**
     * ����� ������� "������� ����� �����"
     * @returns {undefined}
     */
	self.onNewOrder = function() {
		self._saveCookie('api_order_id', '');
		self._saveCookie('api_order_custom_car_id', '');
		self._saveCookie('api_order_custom_car_info', '');
		self._saveCookie('fromLat', '');
		self._saveCookie('fromLon', '');
		self._saveCookie('toLat', '');
		self._saveCookie('toLon', '');
	};
	self.saveGeoData = function(params) {
		self._saveCookie('fromLat', params.fromLat);
		self._saveCookie('fromLon', params.fromLon);
		self._saveCookie('toLat', params.toLat);
		self._saveCookie('toLon', params.toLon);
	};
	self.deleteGeoData = function() {
		self._saveCookie('fromLat', '');
		self._saveCookie('fromLon', '');
		self._saveCookie('toLat', '');
		self._saveCookie('toLon', '');
	};
	/**
     * ����������� �������� �������� ������
     * @param {object} validationResult - ��������� ���������
     * @returns {undefined}
     */
	self.createOrder = function(validationResult) {
		self.lastCreateOrderValidationResult = validationResult;
		self.store.set('lastCreateOrderValidationResult', validationResult);
		console.log('�������� ������');
		console.log(validationResult.paramsToValidate);
		console.log(validationResult.paramsToValidate.fromLat);
		console.log(validationResult.paramsToValidate.fromLon);
		self.saveGeoData(validationResult.paramsToValidate);
		self.lock();
		var createOrderQuery = new TaxiMethod('createOrder');
		createOrderQuery.tryCount = 1;
		createOrderQuery.params = validationResult.paramsToValidate;
		createOrderQuery.successCallback = function(result) {
			var orderId = result;
			if (orderId != '4' && orderId !== '' && orderId !== null && orderId !== false && orderId !== 'server_interanl_error') {
				self.orderId = orderId;
				self.sendForm();
				self._afterCreateOrder(orderId);
				self.afterCreateOrder(orderId);
				self.startOrderInfoUpdating(orderId);
			} else if (orderId == '4') {
				self.unlock();
				var errorsInfo = new TaxiErrorsInfo();
				errorsInfo.summaryHtml = errorsInfo.summaryText = '<p style="color:red">������� ������������ ���������� �������. ��������� ����� 30 ���.</p>';
				errorsInfo.count = 1;
				self.displayErrors(errorsInfo);
			}
			else {
				self.unlock();
				var errorsInfo = new TaxiErrorsInfo();
				errorsInfo.summaryHtml = errorsInfo.summaryText = '�� ������� ������� �����';
				errorsInfo.count = 1;
				self.displayErrors(errorsInfo);
			}

		};
		createOrderQuery.errorCallback = function() {
			console.log('������ ��� ���������� ������� �� �������� ������!');
			self.onError('�������� ������ ��� �������� ������!');
			self.unlock();
		};
		self.client.executeQuery(createOrderQuery);
	};
	/**
     * ����� ����� ����� ��� ����������� ���� � ��� ������ �����, �������� ���
     * @param {type} loginResult
     * @returns {undefined}
     */
	self.afterLogin = function(loginResult) {
		if (loginResult.token && loginResult.browserKey) {
			self.setToken(loginResult.token);
			self.setBrowserKey(loginResult.browserKey);
			console.log("�������� ���������� ����� ��� �����������: " + loginResult.token);
			console.log("�������� ���������� ����: " + loginResult.browserKey);
		}
	};
	/**
     * ���� ����� ���������� �������� ��� ����� �� ������ "��������" - ������������ �������� 
     * �������� �������� ����� - ���� ����� ����� ������ �������, �� ����� ���������
     * �������� �������� �����
     * @returns {undefined}
     */
	self.onSubmitOrderForm = function() {
		if (self.isLocked()) {
			return false;
		}
		self.lock();
		var createOrderQuery = self.createOrderQuery();
		var validateQuery = new TaxiMethod('validateCommand');
		validateQuery.params = {
			command: 'createOrder',
			paramsToValidate: createOrderQuery
		};
		validateQuery.successCallback = function(validationResult) {
			self._lastValidationResult = validationResult;
			if (validationResult.hasErrors) {
				self.displayErrors(validationResult.errorsInfo);
				self.unlock();
			} else {
				console.log(createOrderQuery);
				/**
                 * ��������� ���������� �� ����� �� ����������� ����� ���
                 * @param {object} validationResult
                 */
				self.nextActions = function(validationResult) {
					console.log('nextActions ...');
					self.components.customCar.enabled = false;
					self.phone = validationResult.paramsToValidate.phone;
					self.updateNeedSmsAuthorizationFlag(function(needSendSms) {
						if (needSendSms == 1) {
							self.checkSmsAuthorization(validationResult);
						} else if (needSendSms == 0) {
							self.createOrder(validationResult);
						} else if (needSendSms == 2) {
							alert('��� ������� � ������ ������!');
							self.unlock();
						}
					});
				}
				;
				if (!self.useCustomCarInPriorOrder && createOrderQuery.priorTime && (createOrderQuery.customCar || createOrderQuery.customCar))
				{
					self.modalConfirm(self.modalConfirm_useCustomCarInPriorOrder,
						[{
							label: '���',
							callback: function() {
								self.unlock();
								return false;
							}
						},
						{
							label: '�������',
							callback: function() {
								self.unlock();
								self.nextActions(validationResult);
								return true;
							}
						}]
						);
				} else {
					self.nextActions(validationResult);
				}
			}
		};
		validateQuery.errorCallback = function() {
			self.unlock();
			console.log('������ ��� ���������� �������!');
		};
		self.client.executeQuery(validateQuery);
	};
	/**
     * ������� ��������� ����� �������� �������� ������
     * @param {integer} orderId - �� ���������� ������
     * @returns {undefined}
     */
	self.afterCreateOrder = function(orderId) {
		console.log('������ ����� ' + orderId.toString());
	};
	/**
     * ��������� ����������� ����� �� �����
     * @returns {undefined}
     */
	self.disableUpdatingCars = function() {
		console.log('Disabling updating cars again!');
		self._lastIntervalUpdatingCars = self.intervalUpdatingCars;
		self.intervalUpdatingCars = 0;
	};
	/**
     * �������� ��������� ����� 
     * @returns {undefined}
     */
	self.enableUpdatingCars = function() {
		console.log('Starting updating cars again!');
		if (!self._lastIntervalUpdatingCars) {
			self._lastIntervalUpdatingCars = 10000;
		}
		self.intervalUpdatingCars = self._lastIntervalUpdatingCars;
		taxi.startNextUpdateCarMarks();
	};
	/**
     * �������� � �� ��� ���������� ��������� ����� �� �����
     * @returns {Window.intervalUpdatingCars|Number|self.intervalUpdatingCars}
     */
	self.getUpdateCarsInterval = function() {
		return self.intervalUpdatingCars;
	};
	/**
     * ������� ��������/��������� ������ �� �������� ������, ������ ������, �����
     * � ����������� �� ������� ������
     * @param {string} status - ������ ������
     * @returns {undefined}
     */
	self.HideShowLinks = function(status) {
		switch (status) {
			case 'new':
				$(document).ready(function() {
					$('#reject_order').show();
					$('#send_review').hide();
				});
				break;
			case 'car_assigned':
				$(document).ready(function() {
					$('#reject_order').show();
					$('#send_review').hide();
				});
				break;
			case 'rejected':
				$(document).ready(function() {
					$('#reject_order').hide();
					$('#send_review').hide();
				});
				break;
			case 'car_at_place':
				$(document).ready(function() {
					$('#reject_order').hide();
					$('#send_review').hide();
				});
				break;
			case 'executing':
				$(document).ready(function() {
					$('#reject_order').hide();
					$('#send_review').hide();
				});
				break;
			case 'completed':
				$(document).ready(function() {
					$('#reject_order').hide();
					$('#send_review').show();
				});
				break;
			case 'driver_busy':
				break;
			default:
				break;
		}


	}

	self.addOrderPoints = function() {
		var fromLat = self._readCookie('fromLat');
		var fromLon = self._readCookie('fromLon');
		var toLat = self._readCookie('toLat');
		var toLon = self._readCookie('toLon');
		placemark1 = new ymaps.Placemark([fromLat, fromLon], {//�������������� �����
			iconContent: ""
		}, {
			draggable: false,
			iconImageHref: '/bitrix/templates/taxi_yellow/i/A.png',
			iconImageSize: [41, 36]
		});
		yandexMap.map.geoObjects.add(placemark1);
		placemark2 = new ymaps.Placemark([toLat, toLon], {//�������������� �����
			iconContent: ""
		}, {
			draggable: false,
			iconImageHref: '/bitrix/templates/taxi_yellow/i/B.png',
			iconImageSize: [41, 36]
		});
		yandexMap.map.geoObjects.add(placemark2);
		if ((placemark1) && (placemark2))
		{
			ymaps.route([{
				type: 'wayPoint', 
				point: [fromLat, fromLon]
			}, {
				type: 'wayPoint', 
				point: [toLat, toLon]
			}]).then
			(
				function(router)
				{
					var points = router.getWayPoints();
					points.get(0).properties.set('iconImageHref', '/bitrix/templates/taxi_yellow/i/A.png');
					points.get(0).properties.set('iconImageSize', [41, 36]);
					points.get(1).properties.set('iconImageHref', '/bitrix/templates/taxi_yellow/i/B.png');
					points.get(1).properties.set('iconImageSize', [41, 36]);
					/*router.getPaths().options.set({
						strokeWidth: 5, 
						strokeColor: '0000ffff', 
						opacity: 0.8
					});
					yandexMap.map.geoObjects.add(router.getPaths());*/
				},
				function(error)
				{
				}
				);
		}
	;
	};
	/**
     * �������� ���������� � ��������� ������ � ��������� �����
     * ����� ������� ������� ����������
     * @returns {undefined}
     */
	self.updateOrderInfo = function(after) {
		self.addOrderPoints();
		console.log('Updating order Info ..');
		if (self.hideRejectLink) {
			$(document).ready(function() {
				$('#reject_order').hide();
			});
		}
		if (self._updatingOrder) {
			return false;
		}
		self._updatingOrder = true;
		if (self.orderId !== '') {
			var orderInfoQuery = new TaxiMethod('getOrderInfo');
			orderInfoQuery.params = {
				orderId: self.orderId
			};
			orderInfoQuery.successCallback = function(result) {
				self.unlock();
				if (result) {
					self.HideShowLinks(result.status);
					console.log('STATUS: ' + result.status);
					self.orderIdLabel = result.idLabel;
					self.carId = result.carId;
					self.fromLat = result.fromLat;
					self.fromLon = result.fromLon;
					self.lastOrderInfo = result;
					if (typeof (after) === 'function') {
						after();
					}
					self.displayOrderInfo(result);
				/*
                     * � ���� ��������, ���������� � ������ ���, �.�.
                     * ��� ��� �� � �� - ������� �� ����:
                     */
				} else if (result === false) {
					if (self.lastOrderInfo) {
						self.lastOrderInfo.summaryHtml = $(self.lastOrderInfo.summaryHtml)
						.find('div p').eq(0).append('<p>' + self.messages.errors.onOrderInfoFail + '</p>')
						self.displayLoadingInfo(self.messages.errors.onOrderInfoFail);
					} else {
						self.displayLoadingInfo(self.messages.errors.onOrderInfoFail);
					}
				}
				if (self.components.customCar.enabled) {
					self.components.customCar.onOrderInfoUpdating(self);
				}
				if (!result) {
					// �������� �����
					self.applyTimeoutsRegression(self.intervalOrderInfoUpdating, function(newValue) {
						self.intervalOrderInfoUpdating = newValue;
					});
				}
				setTimeout(function() {
					self.updateOrderInfo();
				}, self.intervalOrderInfoUpdating);
				self._updatingOrder = false;
			};
			orderInfoQuery.errorCallback = function() {
				self.unlock();
				// �������� �����
				self.applyTimeoutsRegression(self.intervalOrderInfoUpdating, function(newValue) {
					self.intervalOrderInfoUpdating = newValue;
				});
				console.log('������ ��� ��������� ���������� � ������!');
				if (self.needForceReAuth) {
					self.updateNeedSmsAuthorizationFlag(function(needSendSms) {
						if (needSendSms == 1) {
							self.forceSmsAuthorization(function() {
								self.updateOrderInfo();
							});
						} else if (needSendSms == 0) {
							self.updateOrderInfo();
						}
					});
				}
				self._updatingOrder = true;
			};
			self.client.executeQuery(orderInfoQuery);
		}
	};
	/**
     * ��������� (��������) �������������� ���������� � ������
     * @param {type} orderId - �� ������, �� �������� ��������� ����������
     * @returns {undefined}
     */
	self.startOrderInfoUpdating = function(orderId) {
		if (orderId.length) {
			self.orderId = orderId;
		}
		$('h4.title_order_info').text('��������� �������. ����� ������. ���������� �� �����y �' + orderId);
		setTimeout(function() {
			self.updateOrderInfo();
		}, self.timeouts.beforeFirstGetOrderInfo);
		return true;
	};
	/*
     * INTERFACE
     */


	/**
     * �������� �������� ����� �� ������
     * @returns {undefined}
     */
	self.sendForm = function() {
		console.log("���������� ������ �����, ������� ��������� submit ����� �� ������");
	};
	/**
     * ���� ���������� ��� �������� ������ � �����
     * @returns {TaxiMethod_createOrder}
     */
	self.createOrderQuery = function() {
		console.log("���������� ������ �����, ������� ��������� ���� ���������� ��� �������� ������ � ����� - @returns TaxiMethod_createOrder");
	};
	/**
     * ���������� ������� ����� �� ���������
     * @param {string} defaultCity - �����
     * @returns {TaxiMethod_createOrder}
     */
	self.setDefaultCity = function(defaultCity) {
		console.log("���������� ������ �����, ������� �������� ����� �� ��������� �� �����" + defaultCity);
	};
	/**
     * ���������� ���������� � ��������� ������� �� ����� ��� ��������
     * @param {TaxiErrorsInfo} errorsInfo
     * @returns {}
     */
	self.displayErrors = function(errorsInfo) {
		console.log("���������� ������ �����, ������� ��������� ����������� ������");
		console.log(errorsInfo);
	};
	/**
     * ���������� ������� ���������� � ������ �� ����� ��� ��������
     * @param {TaxiOrderInfo} orderInfo
     * @returns {undefined}
     */
	self.displayOrderInfo = function(orderInfo) {
		console.log("���������� ������ �����, ������� ��������� ����������� ���������� � ������");
		console.log(orderInfo);
	};
	/**
     * ���������� ������� ���������� � ������ �� ����� ��� ��������
     * @param {string} text
     * @returns {undefined}
     */
	self.displayLoadingInfo = function(text) {
		console.log("���������� ������ �����, ������q ��������� ����������� ���������� � ������� �������� ������");
		console.log(orderInfo);
	};
	/**
     * ��������� ��� ������ ������ �������������
     * @param {function} afterReject ��������� ����� ������ ������, �� �� ���������� ��������� ����������
     * ���� ��� ������� ������ false, �� ���������� ���������� self.afterRejectOrder() ��
     * ����� �������, � �� ����� �������� ���������� ���������� � ������
     * @returns {undefined}
     */
	self.rejectOrder = function(afterReject) {
		if (self.orderId.length) {
			var rejectOrderQuery = new TaxiMethod('rejectOrder');
			rejectOrderQuery.params = {
				orderId: self.orderId
			};
			rejectOrderQuery.successCallback = function(result) {
				self.deleteGeoData();
				if (result) {
					var stop = false;
					if (afterReject) {
						stop = !afterReject();
					}
					if (!stop) {
						self.afterRejectOrder();
						self.updateOrderInfo();
					}
				} else {
					alert('�� ������� �������� �����');
				}
			};
			rejectOrderQuery.errorCallback = function() {
				alert('�� ������� �������� �����');
			};
			self.client.executeQuery(rejectOrderQuery);
		}
	};
	/**
     * ������� ����� ������ �������
     * @returns {undefined}
     */
	self.afterRejectOrder = function() {
		console.log('����� ������� �������');
	};
	/**
     * �������� ���� �� ������������� �������������� ��� ����������� �������     
     * @param {function} doneFunction - ��������� ����� ��������� ���������� �������
     * @returns {boolean} - ������� �� ������
     */
	self.updateNeedSmsAuthorizationFlag = function(doneFunction) {
		var query = new TaxiMethod('needSendSms');
		if (self.phone) {
			query.params = {
				phone: self.phone
			};
		}
		query.successCallback = function(result) {
			doneFunction(result);
		};
		self.client.executeQuery(query);
	};
	/**
     * ���������� ��������� ����
     * @param {type} key
     * @param {type} value
     * @returns {undefined}
     */
	self._saveCookie = function(key, value)
	{
		$.cookie(key, value,
		{
			expires: 365,
			path: "/"
		});
	};
	/**
     * ������� ����, ��� ������ ������ ���������� ���������� ������� ����� ����
     * @param {string} key - ����
     * @returns {unresolved}
     */
	self._readCookie = function(key)
	{
		var res = $.cookie(key);
		if (res) {
			self._updateCookie(key);
		}
		return res;
	};
	/**
     * �������� ���� 
     * @param {type} key
     * @returns {undefined}
     */
	self._updateCookie = function(key)
	{
		self._saveCookie(key, $.cookie(key));
	};
	/**
     * ��������� � ���� ���� 
     * @param {type} key
     * @returns {undefined}
     */
	self.setBrowserKey = function(key) {
		self._browserKey = key;
		self._saveCookie('api_browser_key', key);
	};
	/**
     * �������� ����
     * @returns {string}
     */
	self.getBrowserKey = function()
	{
		return self._browserKey = self._readCookie('api_browser_key');
	};
	/**
     * ��������� � ���� ����� 
     * @param {string} token
     * @returns {undefined}
     */
	self.setToken = function(token) {
		self._token = token;
		self._saveCookie('api_token', token);
	};
	/**
     * �������� �����
     * @returns {string}
     */
	self.getToken = function()
	{
		return self._token = self._readCookie('api_browser_key');
	};
	/**
     * ���������� ���� ����� ��� - ��� �������������
     * ����� ���� � ������������     
     * @returns {undefined}
     */
	self.showEnterSmsCodeWindow = function() {
		console.log("���������� ������ �����, ������� ��������� ����������� ���� ����� ��� ����");
	};
	/**
     * ������� ����� �� ��������� � �������� ��� � ���������
     * @returns {undefined}
     */
	self.updateDefaultCity = function() {
		var cityName = self.getCookie('CITY_NAME_USER');
		if (cityName) {
			self.setDefaultCity(cityName);
		} else {
			self.sendToClient('getDefaultCity', {}, function(cityName) {
				self.setDefaultCity(cityName);
			}, function() {
				});
		}
	};
	self.getCookie = function(name) {
		var matches = document.cookie.match(new RegExp(
			"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
			));
		return matches ? decodeURIComponent(matches[1]) : undefined;
	}
	/**
     * �������� ���� ���������
     * @returns {undefined}
     */
	self.enable = function() {
		self._enabled = true;
	};
	/**
     * ��������� ���� ���������
     * @returns {undefined}
     */
	self.disable = function() {
		self._enabled = false;
	};
	/**
     * �������� �� ����� ���������� � ������
     * @returns {undefined}
     */
	self.updateCustomCarInfo = function() {
		console.log("���������� ������ ����������� ���������� � ��������� ������� ������");
	};
	/**
     * ������������ ����� ������
     * @param {string} carId - �� ������
     * @param {string} label - ���������� � ����� � �.�. ��� ��������
     * @returns {undefined}
     */
	self.selectCustomCar = function(carId, label) {
		taxi.ordering.customCarId = carId;
		taxi.ordering.customCarInfo = label;
		self.updateCustomCarInfo();
	};
	/**
     * �������\�������� ����� ������������ ������
     * @returns {undefined}
     */
	self.removeCustomCarSelection = function() {
		self.selectCustomCar(null, '');
	};
	/**
     * ��������� ��� ������ � ���������� ������
     */
	taxi.modal = new TaxiBitrixModalWindow();
	/**
     * ����� ���������� ���� � ����������� ���������� � �������� ��� ������������� ��������
     * @param {string} message - ��������� - ����� ���� ������ html ���������������� ������
     * @param {array} buttonsOptions - ������ - ������ � ����������� � �������
     * @returns {}
     */
	self.modalConfirm = function(message, buttonsOptions) {
		return taxi.modal.customConfirm(message, buttonsOptions);
	};
	/**
     * ����������� �������������� ����������
     */
	self.autoInit = function() {
		self.sendToClient('getJsInitializationCode', {}, function(code) {
			eval(code);
			/*
             * ��������� ������, ������ ���� ���� ��������� �������
             */
			if (self._enabled) {
				self.updateNeedSmsAuthorizationFlag(function(result) {
					if (result && result.length > 1) {
						if (!self.getBrowserKey()) {
							console.log('�������� ���� ' + result);
							self.setBrowserKey(result);
						}
					}
				});
				self.updateDefaultCity();
			}
		;
		}, function() {
			});
	};
	/**
     * ����������������� ������ ��� �������� ���������
     */
	$(document).ready(function() {
		self.autoInit();
	});
	return self;
}
;