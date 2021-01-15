/* 
 * Процесс создания заказа на сайте
 */

/**
 * Процесс создания заказа на текущем сайте
 * @param {TaxiClient} client - Текущее клиентское подключение для отправки\получения AJAX к нашему клиенту
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
     * Флаг включенности этого подкомпонета - и включения\выключения основных запросов
     */
	self._enabled = true;
	/**
     * Хранилище данных
     */
	self.store = new TaxiDataStore();
	/**
     * Набор подкомпонентов
     */
	self.components = {
		customCar: new TaxiCustomCarComponent(),
		route: new TaxiRouteComponent()
	};
	/**
     * Строка для вопроса - предупреждения
     */
	self.modalConfirm_useCustomCarInPriorOrder = '<p>Для предварительных заказов нельзя выбирать машину самостоятельно. Такие заказы обрабатываются автоматически.</p>' +
	'<p>Машина назначается автоматически. Выбранная вами машина учитываться не будет.</p>' +
	'<p>Создать заказ?</p>';
	/**
     * Строковые сообщения о различных ошибках
     */
	self.messages = {
		errors: {
			onOrderInfoFail: '! Произошла ошибка: не удалось обновить информацию о заказе'
		}
	};
	/**
     * Таймауты, мс
     */
	self.timeouts = {
		/**
         * Дополнительно
         */
		options: {
			/**
             * Увеличение паузы перед повторами или отправки определенных запросов,
             * после неудачных попыток, например обновить информцию о заказе
             */
			regression: {
				/**
                 * Флаг включенности механизма
                 */
				enabled: true,
				/**
                 * Коэффициент увеличения 
                 */
				incresaseOnFailValue: 1.5,
				/**
                 * Лимит возрастания пауз перед повторными отправками запросов
                 */
				limit: 10000
			}
		},
		/**
         * Перед первым получением информации по заказу, после успешного создания заказа
         */
		beforeFirstGetOrderInfo: 9000
	};
	/**
     * Последняя полученная информация по заказу
     */
	self.lastOrderInfo = null;
	/**
     * Обновлять информацию о заказе, после его отправки
     * мсек
     */
	self.intervalOrderInfoUpdating = 3000;
	/**
     * Интервал для обновления свободных машин
     */
	self._lastIntervalUpdatingCars = self.intervalUpdatingCars = 20000;
	/**
     * Номер текущего (обновляемого заказа)ж
     */
	self.orderId = $.cookie('api_order_id');
	/**
     * Произвольный выбор машины
     */
	self.customCarId = null;
	/**
     * Для хранения последнего результата валидации перед созданием заказа
     */
	self.lastCreateOrderValidationResult = self.store.get('lastCreateOrderValidationResult');
	/**
     * Флаг - разрешить использование произвольного выбора машины
     * при предварительном заказе
     */
	self.useCustomCarInPriorOrder = false;
	/**
     * Произвольный выбор машины - информация о выбранной машине
     */
	self.customCarInfo = null;
	/**
     * Внутренний флаг блокировки запрсов для создания заказов
     */
	self._isLocked = false;
	/**
     * Флаг обновления статуса заказа - для предотвращения повторных запросов
     */
	self._updatingOrder = false;
	/**
     * Флаг для принудительной передавторизации (необходимо сейчас временно для
     * седи)
     */
	self.needForceReAuth = false;
	/**
     * Флаг - скрыть ссылку отменить заказ - 
     * если адвптер этого не поддерживает
     */
	self.hideRejectLink = false;
	/**
     * Ключ ! для отладки
     */
	self._browserKey = null;
	/**
     * Токен ! для отладки
     */
	self._token = null;
	/**
     * Храним последний результат валидации при создании заказа
     */
	self._lastValidationResult = null;
	/**
     * Текущая назначенная машина, если уже отрпавлен заказ
     */
	self.carId = null;
	/**
     * Выводим этот номер пользователю
     */
	self.orderIdLabel = null;
	/**
     * Телефон клиента - временное хранение кода
     */
	self.phone = null;
	/**
     * Ввод СМС кода - для сохранения кода после отправки и т.д.
     */
	self.smsCode = null;
	/**
     * Точка посадки
     */
	self.fromLat = null;
	/**
     * Точка посадки
     */
	self.fromLon = null;
	/**
     * Точка куда
     */
	self.toLat = null;
	/**
     * Точка куда
     */
	self.toLon = null;
	/**
     * Возникает при какой-либо ошибке 
     * @param {string} message сообщение об ошибке
     * @param {object} errorInfo информация об ошибке
     * @returns {Boolean}
     */
	self.onError = function(message, errorInfo) {
		alert(message);
	};
	/**
     * Проверить - процесса заказа на финальном этапе:
     * т.е. уже назначена машина?
     * @returns {Boolean}
     */
	self.isOnFinalState = function() {
		return self.carId > 0;
	};
	/**
     * точка посдки в текущем заказе
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
     * точка куда в текущем заказе
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
     * Анимированные дейтсвия перед любой операцией, требующей блокировки
     * @returns {undefined}
     */
	self.animateBusy = function() {

		$('.loader').css('display', 'block');
	};
	/**
     * Анимированные дейтсвия после любой операцией, требующей блокировки
     * @returns {undefined}
     */
	self.animateUnbusy = function() {
		$('.loader').css('display', 'none');
	};
	/**
     * Заблокировать отправку запрсов
     * @returns {undefined}
     */
	self.lock = function() {
		self.animateBusy();
		self._isLocked = true;
		// идет принудительная разблокировка через 30 сек после блокировки
		setTimeout(function() {
			self.unlock();
		}, 30000);
	};
	/**
     * Разблокировать отправку запросов
     * @returns {undefined}
     */
	self.unlock = function() {
		self.animateUnbusy();
		self._isLocked = false;
	};
	/**
     * Проверить блокировку запросов
     * @returns {undefined}
     */
	self.isLocked = function() {
		return self._isLocked;
	};
	/**
     * Текущее клиентское подключение для отправки\получения AJAX к нашему клиенту
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
     * Увеличить таймаут, если включена регрессия, если она выклюена - то просто 
     * будет возвращено старое значение
     * @param {integer} oldValue - старое значение, мс
     * @returns {integer} - новое (увеличенное значние)
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
			console.log('Применена регрессия от ' + oldValue + ' до ' + newValue);
		}
		if (typeof (callback) === 'function') {
			callback(newValue);
		}
		return newValue;
	};
	/**
     * Выполнить запрос на клиента
     * @param {string} methodName - метод АПИ нашего клиента
     * @param {object} params - параметры
     * @param {function} onSuccess - при успешном выполнении
     * @param {function} onFail - (Опционально) - при провале запроса
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
			console.log('Ошибка при отправке запроса!');
		};
		method.params = params;
		self.client.executeQuery(method);
	};
	/**
     * Проверка авторизации, запрос СМС кода и т.д.
     * @param {object} validationResult - результат запроса валидации
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
				alert('Тестовый код -- ' + sendSmsResult.resultCode);
			}
			/*
             * Если уже авторизованны, то незачем запрашивать у пользователя:
             *      - Поэтому сразу создаем заказ, при этом АПИ адаптера
             *      само должно провести авторизацию
             * код, иначе 
             *      - Показываем оксно после запроса авторизации
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
     * ! ДЛЯ СЕДИ
     * Проверка авторизации, запрос СМС кода и т.д.
     * @param {function} onSuccess - результат запроса валидации
     * @returns {undefined}
     */
	self.forceSmsAuthorization = function(onSuccess) {
		if (!self.phone) {
			alert('Невозможно обновить информацию о заказе, так как необходима авторизация!');
			self.phone = prompt("Введите ваш телефон снова:");
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
             * Если уже авторизованны, то незачем запрашивать у пользователя:
             *      - Поэтому сразу создаем заказ, при этом АПИ адаптера
             *      само должно провести авторизацию
             * код, иначе 
             *      - Показываем оксно после запроса авторизации
             */
			if (sendSmsResult.isAuthorizedNow) {
				onSuccess();
			} else {
				self.showEnterSmsCodeWindow();
			}
		});
	};
	/**
     * Создание заказа по последнему результату валидации
     * @returns {undefined}
     */
	self.createLastOrder = function() {
		if (self._lastValidationResult) {
			self.createOrder(self._lastValidationResult);
		}
	};
	/**
     * Сохраним в куки номер созданного заказа
     * @param {type} orderId
     * @returns {undefined}
     */
	self._afterCreateOrder = function(orderId)
	{
		self._saveCookie('api_order_id', orderId);
		self._saveCookie('api_order_custom_car_id', self.customCarId);
		self._saveCookie('api_order_custom_car_info', self.customCarInfo);
		console.log('сохранен в куки номер заказа ' + orderId.toString());
	};
	/**
     * После нажатия "Создать новый заказ"
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
     * Продолжение процесса отправки заказа
     * @param {object} validationResult - результат валидации
     * @returns {undefined}
     */
	self.createOrder = function(validationResult) {
		self.lastCreateOrderValidationResult = validationResult;
		self.store.set('lastCreateOrderValidationResult', validationResult);
		console.log('Отправка заказа');
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
				errorsInfo.summaryHtml = errorsInfo.summaryText = '<p style="color:red">Создано максимальное количество заказов. Поробуйте через 30 мин.</p>';
				errorsInfo.count = 1;
				self.displayErrors(errorsInfo);
			}
			else {
				self.unlock();
				var errorsInfo = new TaxiErrorsInfo();
				errorsInfo.summaryHtml = errorsInfo.summaryText = 'Не удалось создать заказ';
				errorsInfo.count = 1;
				self.displayErrors(errorsInfo);
			}

		};
		createOrderQuery.errorCallback = function() {
			console.log('Ошибка при выполнении запроса на создание заказа!');
			self.onError('Возникла ошибка при создании заказа!');
			self.unlock();
		};
		self.client.executeQuery(createOrderQuery);
	};
	/**
     * После входа через смс авторизацию если к нам пришел токен, сохраним его
     * @param {type} loginResult
     * @returns {undefined}
     */
	self.afterLogin = function(loginResult) {
		if (loginResult.token && loginResult.browserKey) {
			self.setToken(loginResult.token);
			self.setBrowserKey(loginResult.browserKey);
			console.log("Сохранен полученный токен для авторизации: " + loginResult.token);
			console.log("Сохранен полученный ключ: " + loginResult.browserKey);
		}
	};
	/**
     * Этот метод необходимо вызывать при клике на кнопку "Заказать" - предотвращая отправку 
     * реальную отправку формы - если заказ будет создан успешно, то будет выполнена
     * реальная отправка формы
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
                 * Обновляем информацию по флагу об авторизации через СМС
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
							alert('Ваш телефон в чёрном списке!');
							self.unlock();
						}
					});
				}
				;
				if (!self.useCustomCarInPriorOrder && createOrderQuery.priorTime && (createOrderQuery.customCar || createOrderQuery.customCar))
				{
					self.modalConfirm(self.modalConfirm_useCustomCarInPriorOrder,
						[{
							label: 'Нет',
							callback: function() {
								self.unlock();
								return false;
							}
						},
						{
							label: 'Создать',
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
			console.log('Ошибка при выполнении запроса!');
		};
		self.client.executeQuery(validateQuery);
	};
	/**
     * Событие возникает после успешной отправки заказа
     * @param {integer} orderId - ИД созданного заказа
     * @returns {undefined}
     */
	self.afterCreateOrder = function(orderId) {
		console.log('Создан заказ ' + orderId.toString());
	};
	/**
     * Отключить облновление машин на карте
     * @returns {undefined}
     */
	self.disableUpdatingCars = function() {
		console.log('Disabling updating cars again!');
		self._lastIntervalUpdatingCars = self.intervalUpdatingCars;
		self.intervalUpdatingCars = 0;
	};
	/**
     * Включить обноление машин 
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
     * Интервал в мс для обновления свободных машин на карте
     * @returns {Window.intervalUpdatingCars|Number|self.intervalUpdatingCars}
     */
	self.getUpdateCarsInterval = function() {
		return self.intervalUpdatingCars;
	};
	/**
     * Функция скрывает/отображат ссылки на создание заказа, отмену заказа, отзыв
     * в зависимости от статуса заказа
     * @param {string} status - статус заказа
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
		placemark1 = new ymaps.Placemark([fromLat, fromLon], {//инициализируем точку
			iconContent: ""
		}, {
			draggable: false,
			iconImageHref: '/bitrix/templates/taxi_yellow/i/A.png',
			iconImageSize: [41, 36]
		});
		yandexMap.map.geoObjects.add(placemark1);
		placemark2 = new ymaps.Placemark([toLat, toLon], {//инициализируем точку
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
					router.getPaths().options.set({
						strokeWidth: 5, 
						strokeColor: '0000ffff', 
						opacity: 0.8
					});
					yandexMap.map.geoObjects.add(router.getPaths());
				},
				function(error)
				{
				}
				);
		}
	;
	};
	/**
     * Обновить информацию о созданном заказе и запустить снова
     * через таймаут процесс обновления
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
                     * А если например, информации о заказе нет, т.к.
                     * его уже не в БД - напишем об этом:
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
					// Увеличим паузу
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
				// Увеличим паузу
				self.applyTimeoutsRegression(self.intervalOrderInfoUpdating, function(newValue) {
					self.intervalOrderInfoUpdating = newValue;
				});
				console.log('Ошибка при получении информации о заказе!');
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
     * Запустить (включить) автообновление информации о заказе
     * @param {type} orderId - ИД заказа, по которому обновлять информацию
     * @returns {undefined}
     */
	self.startOrderInfoUpdating = function(orderId) {
		if (orderId.length) {
			self.orderId = orderId;
		}
		$('h4.title_order_info').text('Выполнено успешно. Заказ создан. Информация по заказy №' + orderId);
		setTimeout(function() {
			self.updateOrderInfo();
		}, self.timeouts.beforeFirstGetOrderInfo);
		return true;
	};
	/*
     * INTERFACE
     */


	/**
     * Отправка реальной формы на сервер
     * @returns {undefined}
     */
	self.sendForm = function() {
		console.log("Необходимо задать метод, которые выполняет submit формы на сервер");
	};
	/**
     * Сбор информации для создания заказа с формы
     * @returns {TaxiMethod_createOrder}
     */
	self.createOrderQuery = function() {
		console.log("Необходимо задать метод, которые выполняет Сбор информации для создания заказа с формы - @returns TaxiMethod_createOrder");
	};
	/**
     * Установить текущий город по умолчанию
     * @param {string} defaultCity - город
     * @returns {TaxiMethod_createOrder}
     */
	self.setDefaultCity = function(defaultCity) {
		console.log("Необходимо задать метод, который заменяет город по умолчанию на форме" + defaultCity);
	};
	/**
     * Отобразить информацию о возникших ошибках на форме или браузере
     * @param {TaxiErrorsInfo} errorsInfo
     * @returns {}
     */
	self.displayErrors = function(errorsInfo) {
		console.log("Необходимо задать метод, которые выполняет отображения ошибок");
		console.log(errorsInfo);
	};
	/**
     * Отобразить текущую информацию о заказе на форме или браузере
     * @param {TaxiOrderInfo} orderInfo
     * @returns {undefined}
     */
	self.displayOrderInfo = function(orderInfo) {
		console.log("Необходимо задать метод, которые выполняет отображения информации о заказе");
		console.log(orderInfo);
	};
	/**
     * Отобразить текущую информацию о заказе на форме или браузере
     * @param {string} text
     * @returns {undefined}
     */
	self.displayLoadingInfo = function(text) {
		console.log("Необходимо задать метод, которыq выполняет отображения информации о статусе загрузки заказа");
		console.log(orderInfo);
	};
	/**
     * Выполняем при отмене заказа пользователем
     * @param {function} afterReject выполнить после отмены заказа, но до обновления визульной информации
     * если эта функция вернет false, то визуальное обновление self.afterRejectOrder() не
     * будет вызвано, и не будет запущено обновление информации о заказе
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
					alert('Не удалось отменить заказ');
				}
			};
			rejectOrderQuery.errorCallback = function() {
				alert('Не удалось отменить заказ');
			};
			self.client.executeQuery(rejectOrderQuery);
		}
	};
	/**
     * Событие после отмены заказаы
     * @returns {undefined}
     */
	self.afterRejectOrder = function() {
		console.log('Заказ успешно отменен');
	};
	/**
     * Обновить флаг об использовании принудительной СМС авторизации клиента     
     * @param {function} doneFunction - выполнить после успешного выполнения запроса
     * @returns {boolean} - успешно ли прошло
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
     * Внутренняя установка куки
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
     * Вернуть куку, при каждом чтении происходит обновление времени жизни куки
     * @param {string} key - ключ
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
     * Обновить куку 
     * @param {type} key
     * @returns {undefined}
     */
	self._updateCookie = function(key)
	{
		self._saveCookie(key, $.cookie(key));
	};
	/**
     * Сохранить в куки ключ 
     * @param {type} key
     * @returns {undefined}
     */
	self.setBrowserKey = function(key) {
		self._browserKey = key;
		self._saveCookie('api_browser_key', key);
	};
	/**
     * Получить ключ
     * @returns {string}
     */
	self.getBrowserKey = function()
	{
		return self._browserKey = self._readCookie('api_browser_key');
	};
	/**
     * Сохранить в куки токен 
     * @param {string} token
     * @returns {undefined}
     */
	self.setToken = function(token) {
		self._token = token;
		self._saveCookie('api_token', token);
	};
	/**
     * Получить токен
     * @returns {string}
     */
	self.getToken = function()
	{
		return self._token = self._readCookie('api_browser_key');
	};
	/**
     * Отобразить окно ввода смс - при необходимости
     * ввода кода и авториазации     
     * @returns {undefined}
     */
	self.showEnterSmsCodeWindow = function() {
		console.log("Необходимо задать метод, которые выполняет отображение окна ввода смс кода");
	};
	/**
     * Обновим город по умолчанию и сохраним его в хранилище
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
     * Включить этот компонент
     * @returns {undefined}
     */
	self.enable = function() {
		self._enabled = true;
	};
	/**
     * Выключить этот компонент
     * @returns {undefined}
     */
	self.disable = function() {
		self._enabled = false;
	};
	/**
     * Обновить на форме информацию о машине
     * @returns {undefined}
     */
	self.updateCustomCarInfo = function() {
		console.log("Необходимо задать отображение информации о выбранной вручную машине");
	};
	/**
     * Произвольный выбор машины
     * @param {string} carId - ИД машины
     * @param {string} label - информация о марке и т.д. для человека
     * @returns {undefined}
     */
	self.selectCustomCar = function(carId, label) {
		taxi.ordering.customCarId = carId;
		taxi.ordering.customCarInfo = label;
		self.updateCustomCarInfo();
	};
	/**
     * Удалить\отменить выбор произвольной машины
     * @returns {undefined}
     */
	self.removeCustomCarSelection = function() {
		self.selectCustomCar(null, '');
	};
	/**
     * Компонент для работы с модальными окнами
     */
	taxi.modal = new TaxiBitrixModalWindow();
	/**
     * Вывод модального окна с необходимым сообщением и кнопками для подтверждения дейтсвий
     * @param {string} message - сообщение - может быть блоком html форматированного текста
     * @param {array} buttonsOptions - объект - массив с информацией о кнопках
     * @returns {}
     */
	self.modalConfirm = function(message, buttonsOptions) {
		return taxi.modal.customConfirm(message, buttonsOptions);
	};
	/**
     * Динамически инициализируем компоненты
     */
	self.autoInit = function() {
		self.sendToClient('getJsInitializationCode', {}, function(code) {
			eval(code);
			/*
             * Продолжим работу, только если этот компонент включен
             */
			if (self._enabled) {
				self.updateNeedSmsAuthorizationFlag(function(result) {
					if (result && result.length > 1) {
						if (!self.getBrowserKey()) {
							console.log('Сохранен ключ ' + result);
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
     * Автоинициализация только при загрузке документа
     */
	$(document).ready(function() {
		self.autoInit();
	});
	return self;
}
;