/*
 * Скрипт для инициализации формы с заказом:
 * 
 * автокомплита, обработчиков автоподсказки на форме заказе,
 * подкючение карт, настройка карт
 * событий изменения путей, перерасчета стоимостей и т.д., отправки заказов
 * 
 */

// Инициализируем клиент JS для запросов на наш клинет PHP
taxi.taxiClient = new TaxiClient();

/*
 * Инициализируем текущий адаптер запросов и посылаем запрос
 */
// taxi.suggestCaller = new YandexSuggestCaller();
taxi.suggestCaller = new TaxiSuggestCaller();

/*
 * Создаем текущий адаптер процесса заказа 
 */
taxi.ordering = new TaxiBitrixOrderProcess(taxi.taxiClient);

/*
 * Компонент расчеты цен
 * @type TaxiCost
 */
taxi.cost = new TaxiCost;

/*
 * Последняя цена рассчитаная через ПО
 * @type String
 */
taxi.lastCostfromApi = "";

taxi._disableAdapters = false;

/**
 * Выключить работу с нашим клиентом при необходимости
 * @returns {undefined}
 */
taxi.disableAdpaters = function() {

    console.log('disableAdpaters');

    // флаг выклюения адаптеров
    taxi._disableAdapters = true;
    // отключение подсказки через наше АПИ или адаптеры
    taxi.suggestCaller.disableApi();
    // отклюение компоненты для работы с заказами - проверками, отправкой и т.д.
    taxi.ordering.disable();
    // выключим АПИ клиент
    taxi.taxiClient.enabled = false;
};

/**
 * Функция о включении добавления в комментарий стоимости, расчитанной через JS на сайте
 * @returns {undefined}
 */
taxi.enableAddingCostInOrderComment = function() {
    taxi.ordering._oldCreateOrderQuery = taxi.ordering.createOrderQuery;
    taxi.ordering.createOrderQuery = function() {
        var createOrderData = taxi.ordering._oldCreateOrderQuery();
        if (taxi.routesInfoLayout.cost > 0) {
            createOrderData.comment += ' Стоимость с сайта: ' + taxi.routesInfoLayout.cost + ' руб';
        }
        return createOrderData;
    };
};

/**
 * Выключаем показ и отображение маршрутов на карте через построитель яндекса +
 * выключаем расчет стоимости, времени в пути и т.д, т.к. это связанные вещи
 * @returns {undefined}
 */
taxi.disableRoutes = function() {
    // перестаем обновлять информацию о маршруте
    taxi.routeInfoFunction = function() {
    };
    // перестаем рисовать путь    
    taxi.yandexRoutesOpacity = 0.0;
};

/*
 * ?? НЕКОЕ Обновление информации о маршруте
 * @param {type} res
 * @returns {undefined}
 */
taxi.routeInfoFunction = function(res) {
    var len = res.length; // Длина маршрута
    var time = res.time; //время маршрута

    var timeLine = '';
//                    console.log(time);
    if (time + 1 < 1000 * 60) {
        timeLine = '<p>С учетом пробок '
                + Math.round(time / 60) + ' мин.</p>';
    }
    // сохраним информацию о длине пути
    if (len > 0) {
        $('#list').attr('route-length', Math.round(len));
    }
    if (Math.round(time / 60) > 0) {
        $('#list').attr('route-timeLine', Math.round(time / 60));
    }

    // расчет стоимости делаем через компонент расчета:
    var routeData = new TaxiCostRouteData();
    routeData.len = len;
    routeData.timeInMinutes = Math.round(time / 60);
    routeData.rawFrom = '';
    routeData.rawTo = '';

    //Время предзаказа
    if (taxi.ordering.useNewTemplate()) {
        routeData.priorTime = $('.order-taxi-when-date input').val();
    } else {
        routeData.priorTime = $('label[for=FIELD_DATA] ~ .controls input').val();
    }

    var cost = taxi.cost.callCost(routeData);

    $('#list').html(
            '<div class="control-group rez"><label class="control-label"><span class="rect"></span></label><div class="controls"><p>Расстояние '
            + Math.round(len / 1000) + ' км</p>'
            + timeLine
            + '<p>Примерная стоимость <span id="cost_order" style="color: white;">'
            + Math.round(cost) + '</span><span id="cost_order_from_api" style="color: white;">' + taxi.lastCostfromApi + '</span></p> <span id="range_travel" style="display: none;">'
            + Math.round(len / 1000) + '</span></div></div>'

            );
};

/**
 * Слоя для вывода информации о марсшруте в новой верстке
 * @type 
 */
taxi.routesInfoLayout = {
    _config: {
        cost: ['.order-taxi-data span:has(.price)', 'strong', ' &#8399;', 'cost'],
        distance: ['.order-taxi-data span:has(.distance)', 'strong', ' км', 'distance'],
        time: ['.order-taxi-data span:has(.time)', 'strong', ' мин.', 'time']
    },
    _checkRange: function(value) {
        return value >= 1 && value < 10000;
    },
    _setContol: function(_configLine, value) {
        var baseControl = $(_configLine[0]);
        if (baseControl.length > 0) {
            if (value.length === 0 || !this._checkRange(value)) {
                baseControl.hide().find(_configLine[1]).html('');
            } else {
                baseControl.show().find(_configLine[1]).html(value + _configLine[2]);
                this[_configLine[3]] = value;
            }
        }
    },
    distance: null,
    time: null,
    cost: null,
    /**
     * Обновить расстояние
     * @param {type} distance
     * @returns {undefined}
     */
    setDistance: function(distance) {
        this._setContol(this._config.distance, distance);
    },
    /**
     * Обновить время в пути
     * @param {type} time
     * @returns {undefined}
     */
    setTime: function(time) {
        this._setContol(this._config.time, time);
    },
    /**
     * Обновить стоимость поездки
     * @param {type} cost
     * @returns {undefined}
     */
    setCost: function(cost) {
        this._setContol(this._config.cost, cost);
    },
    /**
     * Обновить стоимость поездки
     * @returns {undefined}
     */
    hideAll: function() {
        this.setCost(0);
        this.setDistance(0);
        this.setTime(0);
    }
};
/*
 * ?? НЕКОЕ Новое Обновление информации о маршруте
 * @param {type} res
 * @returns {undefined}
 */
taxi.newRouteInfoFunction = function(res) {
    var length = res.length; // Длина маршрута
    var time = res.time; //время маршрута

    // сохраним информацию о длине пути
    if (length > 0) {
        $('#list').attr('route-length', Math.round(length));
    }

    if (time > 0) {
        $('#list').attr('route-timeLine', Math.round(time));
    }
    var cost = taxi.callCost(length, time);

    var info = {};
    info.cost = Math.round(cost);
    info.distance = Math.round(length / 1000);
    info.time = Math.round(time / 60);

    taxi.routesInfoLayout.setCost(info.cost);
    taxi.routesInfoLayout.setDistance(info.distance);
    taxi.routesInfoLayout.setTime(info.time);
};

// Навешиваем обработчики событий
jQuery(document).ready(function($) {

    taxi.routesInfoLayout.hideAll();

    /**
     * Другая функция пересчета стоимости
     */
    if (taxi.ordering.useNewTemplate()) {
        taxi.routeInfoFunction = taxi.newRouteInfoFunction;
    };

    /**
     * Отменить заказ на странице заказа
     */
    $('#reject_order').on('click', function(event) {
        event.stopPropagation();
        event.preventDefault();
        var _this = $(this);

        if (_this.hasClass('active') && confirm('Вы действительно хотите отменить заказ?')) {
            if (taxi.ordering.orderId) {
                taxi.ordering.rejectOrder();
            }
        }
    });

    /**
     * Новый заказ на странице заказа
     */
    $('#new_order').on('click', function(event) {
        event.stopPropagation();
        event.preventDefault();
        taxi.ordering.onNewOrder();

        if (taxi.ordering.useNewTemplate()) {
            window.location.href = '/order/';
        } else {
            window.location.href = '/';
        }
    });

    /**
     * Перехват нажатия кнопки "Заказать"
     */
    $('#send_order_form').click('click', function(event) {
        $('.errortext').remove();
        if (!taxi._disableAdapters) {
            var _this = $(this);
            if (!_this.hasClass('stopOrderProccess')) {
                event.stopPropagation();
                event.preventDefault();

                taxi.ordering.onSubmitOrderForm();
            }
        }
        ;
    });

    // виджет автокомплита
    $.widget("ThreeColors.geo_autocomplete", {
        _init: function() {
            this.options._cache = {};
            this.element.autocomplete(this.options)._renderItem = function(_ul, _item) {
                return $('<li></li>').data('item.autocomplete', _item).append(this.options.getItemHTML(_item)).appendTo(_ul);
            };
        },
        options: {
            minLength: 3,
            delay: 300,
            // Тип контрола, который запрашиват функцию source - улица, населенный пункт, только город - street, locality, city
            inputType: 'street',
            /**
             * Функция для прописовки автокомплита путем запросов к внешним геокодерам
             * и получением от них списка вариантов
             * @param {object} _request - объект запроса с .term = 'Пушкинск' - запрошенной строкой для автоподсказки
             * @param {function} _responseCallback - вызвать эту функцию с аргументов ответа от геокодера - это просто массив объектов должен быть: _responseCallback(geocoderAnswer)
             * @returns {undefined}
             */
            source: function(_request, _responseCallback) {
                var city = this.options.city.val() || window.city;
                var cityId = 0;
                if (parseInt(city) > 0) {
                    var cityId = city;
                    var city = this.options.city.find('option:selected').text();
                }
                var house = this.options.house.val() || "";
                var housing = this.options.housing.val() || "";

                /**
                 * Образование большой поисковой строки для Саггеста Яндекса
                 */
                var createTerm = function(obj) {
                    var res = _request.term;
                    // Образование поисковой строки: добави город в критерий поиска / или Россию / страну
                    if (obj.options.inputType === 'street' && city.length > 0) {
                        res = city + ", " + res;
                    } else if (obj.options.inputType === 'locality') {
                        res = 'Россия, ' + res;
                    } else if (obj.options.inputType === 'city') {
                        res = 'Россия, город' + res;
                    }
                    return res;
                };

                var self = this;
                var _address = _request.term;
                var fullAddress = createTerm(this);
                taxi.suggestCaller.city = city;
                taxi.suggestCaller.search({
                    short: _address,
                    long: fullAddress
                }, function(responseObject) {
                    if (!responseObject) {
                        return;
                    }
                    self.options._cache[_request.term] = responseObject.variants;
                    _responseCallback(responseObject.variants);
                });
//                    Раньше обращались к геокодеру
//                    this.options.geocoder(_address, 10, function(geocoderAnswer) {
//                        if (!geocoderAnswer)
//                            return;
//                        self.options._cache[_request.term] = geocoderAnswer;
//                        console.log(geocoderAnswer);
//                        _responseCallback(geocoderAnswer);
//                    });
            },
            /**
             * ? для более красивого отображения выпадающего списка найденных значений
             * @param {type} _item - исходный элменет списка
             * @returns {string} - ? полученный элемент списка в виде выпадающей разметки
             */
            getItemHTML: function(_item) {
                console.log(item);
                return _item.label.replace(/,/gi, ',<br/>');
            }
        }
    });

    // Запретим сабмит формы при нажатии Enter внутри полей формы
    // При нажатии клавиш в контролах запускаем таймаут для автообновлений
    $('#FIELD_FROM, #FIELD_FROM_HOUSE, #FIELD_FROM_HOUSING,#FIELD_TO, #FIELD_TO_HOUSE, #FIELD_TO_HOUSING').keypress(function(event) {
        var _this = $(this);
        // Время задержки перед автообновлением пути на карте после нажатия клавиш
        var lockTime = 1000;
        var isFromControl = _this.closest('.CITY_OTKUDA-group').length > 0 || _this.closest('.FROM_HOUSE-group').length > 0;
        // нажали явным образом enter внутри поля ввода
        if (event.keyCode === 13) {
            event.stopPropagation();
            event.preventDefault();
            taxi.updateRoute(isFromControl);
        } else {
            _this.data('locked', true);
            _this.data('pushed', true);
            // нажали любую другую клавишу - пробуем обновиться
            function runAutoUpdate(sender) {
                setTimeout(function() {
                    if (sender.data('pushed') && !sender.data('locked')) {
                        taxi.updateRoute(isFromControl);
                        sender.data('pushed', false);
                        sender.data('locked', false);
                    } else if (sender.data('pushed') && sender.data('locked')) {
                        sender.data('locked', false);
                        runAutoUpdate(sender);
                    }
                }, lockTime);
            }
            /*
             * Сброс точки из автокомплита
             */
            taxi.ordering.components.route.resetHotNeedUpdate(isFromControl);
            runAutoUpdate(_this);
        }
    });

    // Обновление маршрута при смене города
    $('#FIELD_CITY_OTKUDA, #FIELD_CITY_KUDA').on('blur', function(event) {
        var city = $(this).text();
        var _this = $(this);
        var isFromControl = _this.is('#FIELD_CITY_OTKUDA');
        taxi.updateRoute(isFromControl);
    });

    $("#map").each(function() {
        // Настойка входных инпутов с адресами откуда и куда
        var inputFromStreet = $("#FIELD_FROM"); //Улица
        var inputFromHouse = $("#FIELD_FROM_HOUSE"); //Дом
        var inputFromHousing = $("#FIELD_FROM_HOUSING"); //Корпус
        var inputFromCity = $('#FIELD_CITY_OTKUDA');
        var FROM_HOUSE = $(".FROM_HOUSE-group"); // ? Группа контролов

        var inputToStreet = $("#FIELD_TO"); //Улица
        var inputToHouse = $("#FIELD_TO_HOUSE"); //Дом
        var inputToHousing = $("#FIELD_TO_HOUSING"); //Корпус
        var inputToCity = $('#FIELD_CITY_KUDA');
        var TO_HOUSE = $(".TO_HOUSE-group"); // ? Группа контролов

        /**
         * Обработчик события конца переноса точки на карте
         * @param {object} geoPoint - геобъект от Яндекс карт - с этой точкой и т.д.
         * @param {boolean} isFromPoint - геобъект от Яндекс карт - с этой точкой и т.д.
         * @returns 
         */
        taxi.onDragEnd = function(geoPoint, isFromPoint) {
            if (!geoPoint) {
                return;
            }
            /**
             * Исправление и парсинг данных из строки
             * Республика Марий Эл, Йошкар-Ола, Коммунальная улица, 16
             * 
             * @param {type} geoObject
             * @returns {undefined}
             */
            function _fixGeoObject(geoObject) {
                if (geoObject.street === geoObject.value && geoObject.city === "") {
                    var s = geoObject.value;
                    // console.log(geoObject);
                    var m = s.match(/,(([^,]*улица|проспект|проезд|переулок|площадь)[^,]*)(,(.*)|(.*))$/i);
                    // console.log();
                    if (m) {
                        if (m[1]) {
                            geoObject.street = m[1].trim();
                        }
                        if (m[4]) {
                            geoObject.house = m[4].trim().replace(/^,/, '');
                        }
                    }
                }

            }

            _fixGeoObject(geoPoint);
            if (isFromPoint) {
                /*  if (geoPoint.city === 'undefined' || geoPoint.city == null || geoPoint.city == "") {
                 inputFromCity.val(window.city);
                 }
                 else {
                 inputFromCity.val(geoPoint.city);
                 }*/

                taxi.ordering.fromLat = geoPoint.point[0];
                taxi.ordering.fromLon = geoPoint.point[1];
            } else {
                /*   if (geoPoint.city === 'undefined' || geoPoint.city == null || geoPoint.city == "") {
                 inputToCity.val(window.city);
                 }
                 else {
                 inputToCity.val(geoPoint.city);
                 }*/
                taxi.ordering.toLat = geoPoint.point[0];
                taxi.ordering.toLon = geoPoint.point[1];
            }
        };

        function getCityRow(res) {
            if (typeof (res.city) !== 'undefined' && res.city !== null && res.city !== window.city) {
                var city = res.city;
            } else {
                var city = window.city;
            }
            return (city) ? city : '';
        }
        // обновление адресов при драге точки начала/конца пути
        function startInputSetValues(res, ignore) {
            taxi.onDragEnd(res, true);
            if (!res)
                return;
            ignore = ignore || '';
            if (!res.changed) {
                res.value = res.street;
                res.changed = true;
            }
            if (ignore.indexOf('value') < 0) {
                inputFromStreet.val(res.value);
            }

            if (ignore.indexOf('city') < 0) {
                inputFromCity.val(res.city);
            }

            if (ignore.indexOf('house') < 0) {
                inputFromHouse.val(res.house);
            }
            if (ignore.indexOf('housing') < 0) {
                inputFromHousing.val(res.housing);
            }
            return res;
        }
        // обновление адресов при драге точки начала/конца пути
        function endInputSetValues(res, ignore) {
            taxi.onDragEnd(res, false);
            if (!res)
                return;
            ignore = ignore || '';
            if (!res.changed) {
                res.value = res.street;
                res.changed = true;
            }
            if (ignore.indexOf('value') < 0) {
                inputToStreet.val(res.value);
            }
            if (ignore.indexOf('city') < 0) {
                inputToCity.val(res.city);
            }
            if (ignore.indexOf('house') < 0) {
                inputToHouse.val(res.house);
            }
            if (ignore.indexOf('housing') < 0) {
                inputToHousing.val(res.housing);
            }
            return res;
        }
        /**
         * поставляем значение города по умолчанию
         * решение о сокрытии\показе доп. полей формы
         * @returns {undefined}
         */
        function writeDefaultCity() {
            if (!inputFromStreet.val()) {
                inputFromCity.val(window.city);
            } else {
                FROM_HOUSE.show();
            }
            if (!inputToStreet.val()) {
                inputToCity.val(window.city);
            } else {
                TO_HOUSE.show();
            }
        }
        /**
         * Инициализация объекта для расчета стоимостей и работы с картой, создание карт и т.д.
         * @returns {undefined}
         */
        function initTaxiMapObjects() {

            /**
             * Функция постобработки стоимости
             * @param {float} cost
             * @returns {float}
             */
            taxi.afterCallCost = function(cost) {
                return cost;
            };

            /**
             * Расчет стоимости поездки
             * @param {float} length - длина маршрута или 0
             * @param {float} time - время поездки
             * @returns {float}
             */
            taxi.callCost = function(length, time) {

                // цена за километр или за минуту
                var mileage = $('#tariff_travel option:selected').data('mileage');
                // цена за посадку
                var landing = $('#tariff_travel option:selected').data('landing');
                // цена за количество включенных километров или за количество включенных минут
                var included = $('#tariff_travel option:selected').data('included');
                // минимальная цена поездки
                var minpricecity = $('#tariff_travel option:selected').data('minpricecity');
                // тип тарифа (0 - по километражу, 1 - по времени)
                var typetariff = $('#tariff_travel option:selected').data('typetariff');

                var cost = 0;


                $('.dop_input:checked').each(function() {
                    dop_cost += Math.round($(this).data('cost'));
                });

                if (typetariff == '0' || typetariff == '' || typetariff == undefined) {
                    console.log('По Километражу');

                    if ((length / 1000) > included)
                    {
                        cost = landing + (length / 1000 - included) * mileage;
                    }
                    else
                    {
                        cost = landing;
                    }
                    if (minpricecity > cost)
                    {
                        cost = minpricecity;
                    }
                } else if (typetariff == '1') {
                    console.log('По времени');
                    if (time > included) {
                        cost = landing + (time - included) * mileage;
                    } else {
                        cost = landing;
                    }
                    if (minpricecity > cost)
                    {
                        cost = minpricecity;
                    }
                }


                var finalCost = Math.round(cost);
                finalCost = taxi.afterCallCost(finalCost);
                taxi.routesInfoLayout.cost = finalCost;
                return finalCost;
            };

            taxi.init({
                mapcontainer: "map", // id элемента карты
                map: window.geoservice, //сервис карт yandex|google
                googlemaptoken: '',
                geocoder: window.geoservice, //геокодер сервиса карт yandex|google. на данный момент гекокодер яндекса возможно использовать только с картой яндекса
                city: window.city, //город по умолчанию
                startInput: startInputSetValues,
                endInput: endInputSetValues,
                region: 'ru',
                routeinfo: function(res) {
                    taxi.routeInfoFunction(res);
                }
            });
            // ? Перерасчет стоимости при смене класса обслуживания такси
            $('#tariff_travel').on('change', function() {
                var routeLength = $('#list').attr('route-length');
                var routeTimeLine = $('#list').attr('route-timeLine');
                if (routeLength > 0) {
                    var cost = taxi.callCost(routeLength, routeTimeLine);
                    $('#cost_order').text(cost);
                }
            });

            $('.dop_input').on('change', function() {
                var cost = parseInt($('#cost_order').text());
                if ($(this).is(':checked'))
                    $('#cost_order').text(cost + Math.round($(this).data('cost')));
                else
                    $('#cost_order').text(cost - Math.round($(this).data('cost')));
            });



        }
        /**
         * Автозаполнение полей города при пустых значениях
         */
        function initCityAutoFill() {
            $.each([inputFromCity, inputToCity], function() {
                $(this).on('blur', function() {
                    var _this = $(this);
                    if ($.trim(_this.val()).length === 0) {
                        _this.val(window.city);
                    }
                });
            });
        }
        /**
         * Инициализация сервиса поиска меня на карте через IP
         * @returns {undefined}
         */
        /*  function initFindMyGeoLocation() {
         if (geo_position_js.init()) {
         var findme = $('#find-me');
         var findme2 = $('#find-me2');
         peloader = $("<img>").attr("src", "/preloader.gif").css({"margin": "7px 5px 0 -24px", "float": "right"});
         peloader2 = $("<img>").attr("src", "/preloader.gif").css({"margin": "7px 5px 0 -24px", "float": "right"});
         findme.html('<div class="back target"></div>')
         .css({
         "cursor": "pointer",
         "color": "#08c"
         })
         .before(peloader.hide())
         .on('click', function(e) {
         findme.hide();
         e.preventDefault();
         peloader.show();
         FROM_HOUSE.show();
         geo_position_js.getCurrentPosition(function(res) {
         taxi.mypos(res, function() {
         peloader.hide();
         findme.show();
         });
         }, function() {
         
         });
         });
         
         
         
         findme2.html('<div class="back target2"></div>')
         .css({
         "cursor": "pointer",
         "color": "#08c"
         })
         .before(peloader.hide())
         .on('click', function(e) {
         findme2.hide();
         e.preventDefault();
         peloader2.show();
         TO_HOUSE.show();
         geo_position_js.getCurrentPosition(function(res) {
         taxi.mypos2(res, function() {
         peloader2.hide();
         findme2.show();
         });
         }, function() {
         
         });
         });
         }
         }*/

        function initFindMyGeoLocation() {
            if (geo_position_js.init()) {
                var findme = $('#find-me');
                var findme2 = $('#find-me2');
                peloader = $("<img>").attr("src", "/preloader.gif").css({"margin": "7px 5px 0 -24px", "float": "right"});
                peloader2 = $("<img>").attr("src", "/preloader.gif").css({"margin": "7px 5px 0 -24px", "float": "right"});
                findme.html('<div class="back target" id="back_target"></div>')
                        .css({
                            "cursor": "pointer",
                            "color": "#08c"
                        })
                        .before(peloader.hide())
                        .on('click', function(e) {
                            findme.hide();
                            e.preventDefault();
                            peloader.show();
                            FROM_HOUSE.show();
                            geo_position_js.getCurrentPosition(function(res) {
                                taxi.mypos(res, function() {
                                    peloader.hide();
                                    findme.show();
                                });
                            }, function() {

                            });
                        });


                findme2.html('<div class="back target" id="back_target2"></div>')
                        .css({
                            "cursor": "pointer",
                            "color": "#08c"
                        })
                        .before(peloader2.hide())
                        .on('click', function(e) {
                            findme2.hide();
                            e.preventDefault();
                            peloader2.show();
                            TO_HOUSE.show();
                            geo_position_js.getCurrentPosition(function(res) {
                                taxi.mypos2(res, function() {
                                    peloader2.hide();
                                    findme2.show();
                                });
                            }, function() {

                            });
                        });
            }
        }
        initTaxiMapObjects();
        writeDefaultCity();
        initCityAutoFill();
        initFindMyGeoLocation();

        // Включение автокомплита для улицы, событий при фокусе и потере фокуса входными инпутами

        /**
         * Инициализация автокомплита для группы контролов
         * @param {type} autoCompleteOptions
         * @returns {undefined}
         */
        function initAutoComplete(autoCompleteOptions) {
            // автокоплит для города
            /* autoCompleteOptions.cityInput.geo_autocomplete({
             city: autoCompleteOptions.cityInput,
             house: autoCompleteOptions.houseInput,
             housing: autoCompleteOptions.housingInput,
             // ищем именно насленный пункт
             inputType: 'locality',
             close: function(event, ui) {
             // обрезка всего лишнего      
             //var isFromControl = (autoCompleteOptions.wayPointIndex == 0);
             //taxi.updateRoute(isFromControl);
             //var old = autoCompleteOptions.cityInput.val() + "";
             //old = old.replace(/,.*$/g, '');
             //autoCompleteOptions.cityInput.val(old);
             }
             });*/
            // автокомплит для улицы
            autoCompleteOptions.streetInput.geo_autocomplete({
//                geocoder: taxi.geocoder,
                city: autoCompleteOptions.cityInput,
                house: autoCompleteOptions.houseInput,
                housing: autoCompleteOptions.housingInput,
                inputType: 'street',
                /**
                 * Обработка события выбора элемента выпадающего списка
                 * @param {type} event
                 * @param {type} ui
                 * @returns {undefined}
                 */
                select: function(event, ui) {
                    var isFromControl = (autoCompleteOptions.wayPointIndex === 0);
                    if (typeof (ui.item.geoData) !== 'undefined'
                            &&
                            typeof (ui.item.geoData.location) !== 'undefined' && ui.item.geoData.location) {
                        console.log('updatingGeoData');

                        var geoData = ui.item.geoData;

                        //console.log(geoData);

                        var geoData = ui.item.geoData;
                        taxi.ordering.components.route.onGeoObjectSelect(geoData, isFromControl);

                        if (typeof (geoData) !== 'undefined') {
                            if (typeof (geoData.address) !== 'undefined') {
                                if (typeof (geoData.address.house) !== 'undefined') {
                                    var house = geoData.address.house;
                                    if (house !== '') {
                                        $('#FIELD_FROM_HOUSE').val(house);
                                    }
                                }
                            }
                        }
                    } else {
                        taxi.ordering.components.route.resetHotNeedUpdate(isFromControl);
                    }
                },
                /*
                 * Обработка закрытия выпадающего списка
                 * @param {type} event
                 * @param {type} ui
                 * @returns {undefined}
                 */
                close: function(event, ui) {
                    // обрезка всего лишнего      
                    var isFromControl = (autoCompleteOptions.wayPointIndex === 0);
                    taxi.updateRoute(isFromControl);
                    var old = autoCompleteOptions.streetInput.val() + "";
                    // old = old.replace(/,.*$/g, '');
                    //old = old.replace(/,.*/g, '');
                    autoCompleteOptions.streetInput.val(old);

                    // Раньше устанавливалась точка начала или конца маршрута, сейчас же ничего не делаем просто обновляем улицу\город\дом и т.д.
                    // autoCompleteOptions.taxiSetPointFunction(ui.item);                    
                }
            }).on('focus', function() {
                autoCompleteOptions.hiddenControls.show();
            }).on('blur', function() {
                if (!$(this).val()) {
                    autoCompleteOptions.taxiSetPointFunction(null);
                    autoCompleteOptions.hiddenControls.hide().find('input').each(function() {
                        $(this).val('');
                    });
                } else {
                    autoCompleteOptions.hiddenControls.show();
                }
            });

            /**
             * Функция для обновления маршрута от или до точки
             * Согласно текущему адресу
             */
            var currentUpdateFunction = function() {
                console.log('Updating route');
                var city = autoCompleteOptions.cityInput.val() || window.city;
                if (parseInt(city) > 0) {
                    city = autoCompleteOptions.cityInput.find('option:selected').text();
                }
                var _address =
                        city + ", "
                        + autoCompleteOptions.streetInput.val() + ", "
                        + autoCompleteOptions.houseInput.val()
                        + ((autoCompleteOptions.housingInput.val()) ? " к " + autoCompleteOptions.housingInput.val() : "");
                /*
                 * Запрос текущей строки с адресом к яндекс-геокодеру, обновление точки маршрута?
                 */
                var isFrom = autoCompleteOptions.wayPointIndex === 0;
                if (!taxi.ordering.components.route.hotNeedUpdate(isFrom)) {
                    /*
                     * Если не нужно горячее обновление точки через автокомплит,
                     * то обновляемся через геокодер
                     */
                    taxi.geocoder(_address, 1, function(res) {
                        if (res.length === 0 || (!res[0].street && !res[0].city)) {
                            // taxi.startpoint(null,'value,city, house');
                            return;
                        }
                        console.log('nash gorod' + city);
                        console.log('otvet geocodera' + res[0].city);                    

                        if (res[0].city.toUpperCase() != city.toUpperCase()) {return;}




                        // autoCompleteOptions.taxiSetPointFunction(null);
                        taxi.wayPointsData[autoCompleteOptions.wayPointIndex] = res[0].label;
                        autoCompleteOptions.taxiSetPointFunction(res[0], 'value,city,house,housing');
                    });
                } else {
                    /*
                     * Обновляемся через компонент произвольных (диспечтерских координат) для маршрута
                     */
                    var point = taxi.ordering.components.route.createTaxiRoutePoint(isFrom);
                    autoCompleteOptions.taxiSetPointFunction(point, 'value,city,house,housing');
                }
            };

            // Строим маршруты при изменении инпутов через некоторое время
            $.each([autoCompleteOptions.houseInput, autoCompleteOptions.housingInput], function() {
                $(this).on('focus', function() {
                    $(this).data('val', $(this).val());
                }).on('blur', function() {
                    // замена на русские буквы для букв копрусов зданий
                    autoCompleteOptions.houseInput.val(
                            autoCompleteOptions.houseInput.val().replace("a", "а").replace("b", "б")
                            );
                });
            });

            var res = {
                updateFunction: currentUpdateFunction
            };
            return res;
        }
        var tmp = initAutoComplete({
            hiddenControls: FROM_HOUSE,
            streetInput: inputFromStreet,
            cityInput: inputFromCity,
            houseInput: inputFromHouse,
            housingInput: inputFromHousing,
            taxiSetPointFunction: taxi.startpoint,
            wayPointIndex: 0
        });
        taxi.updateFromFunction = tmp.updateFunction;
        var tmp = initAutoComplete({
            hiddenControls: TO_HOUSE,
            streetInput: inputToStreet,
            cityInput: inputToCity,
            houseInput: inputToHouse,
            housingInput: inputToHousing,
            taxiSetPointFunction: taxi.endpoint,
            wayPointIndex: 1
        });
        taxi.updateToFunction = tmp.updateFunction;

        /**
         * Функция для обновления роута через введенные адреса
         * @returns {undefined}
         */
        taxi.updateRoute = function(isFromControl) {
            if (typeof (isFromControl) === 'undefined') {
                taxi.updateFromFunction();
                taxi.updateToFunction();
            } else {
                if (isFromControl) {
                    taxi.updateFromFunction();
                } else {
                    taxi.updateToFunction();
                }
            }
        };
    });

});
