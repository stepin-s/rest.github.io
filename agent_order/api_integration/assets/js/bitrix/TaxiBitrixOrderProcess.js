/**
 * Процесс создания заказа на текущем сайте
 * TaxiOrderProcesss
 * @param {TaxiClient} client - Текущее клиентское подключение для отправки\получения AJAX к нашему клиенту
 * @returns {TaxiBitrixOrderProcess}
 */
var TaxiBitrixOrderProcess = function(client) {
    var self = new TaxiOrderProcess(client);

    /**
     * Обновить информацию о произвольно выбранной машине
     * @returns {undefined}
     */
    self.updateCustomCarInfo = function() {
        var html = '';
        if (self.useNewTemplate()) {
            /*
             * Случай для новой верстки
             */
            if ($('.custom_car_info').length === 0) {
                $('p:has("#FIELD_FIO")').before('<div class="custom_car_info">');
            }
            html = '<div style="display: block; margin: 10px 5px;" class="control-group   no_quickly">'
                    + ''
                    + ''
                    + ''
                    + ''
                    + '<div class="custom_car_info"><p style="color: #000">Выбрано авто: '
                    + self.customCarInfo
                    + '  <a class="remove_custom_car" style="color: #555; cursor: pointer;">Отменить</a></p></div>'
                    + '</div>'
                    + '';
        } else {
            html = '<div style="display: block;" class="control-group   no_quickly">'
                    + '<label for="FIELD_TYPE_AUTO" class="control-label ">'
                    + 'Выбор машины'
                    + '</label>'
                    + '<div class="controls">'
                    + '<div class="custom_car_info"><p style="color: #FFF">Выбрано авто: '
                    + self.customCarInfo
                    + '  <a class="remove_custom_car" style="color: #999; cursor: pointer;">Отменить</a></p></div>'
                    + '</div>'
                    + '</div>';
        }

        if (self.customCarInfo && self.customCarId > 0) {

            $('.custom_car_info').html(html);
        } else {
            $('.custom_car_info').html('');
        }
        ;
        /**
         * Удалить выбор машины, выбранной вручную
         */
        $('.remove_custom_car').on('click', function(event) {
            event.stopPropagation();
            event.preventDefault();
            var _this = $(this);
            self.removeCustomCarSelection();
        });
    };

    /**
     * После создания скрываем, если есть поле выбора машины
     * @returns {undefined}
     */
    self.afterConstruct = function() {
        $('#FIELD_TEL').closest('.control-group').before('<div class="custom_car_info"></div>');
        self.updateCustomCarInfo();
    };

    /**
     * После создания и инициализации документа
     */
    jQuery(document).ready(function($) {
        self.afterConstruct();
    });

    /**
     * Отправка реальной формы на сервер
     * @returns {undefined}
     */
    self.sendForm = function() {
        if (self.useNewTemplate()) {
            setTimeout(function() {
                $('.order-taxi-inner form[name=iblock_add]').submit();
            }, 3000);
        } else {
            $('.knopka input').addClass('stopOrderProccess');
            $('.knopka button[name=iblock_submit]').click();
        }
    };

    /**
     * Сбор информации для создания заказа с формы
     * @returns {TaxiMethod_createOrder}
     */
    self.createOrderQuery = function() {
        var query = new TaxiMethod_createOrder();
console.log(taxi.ordering);
        query.fromCity = $('#FIELD_CITY_OTKUDA').val();
        query.fromStreet = $("#FIELD_FROM").val(); //Улица
        query.fromHouse = $("#FIELD_FROM_HOUSE").val(); //Дом
        query.fromHousing = $("#FIELD_FROM_HOUSING").val(); //Корпус
        query.fromPorch = $("#FIELD_FROM_PORCH").val(); //Подъезд

        query.fromLat = taxi.ordering.fromLat + ''; //Координты
        query.fromLon = taxi.ordering.fromLon + ''; //Координты

        query.toCity = $('#FIELD_CITY_KUDA').val();
        query.toStreet = $("#FIELD_TO").val(); //Улица
        query.toHouse = $("#FIELD_TO_HOUSE").val(); //Дом
        query.toHousing = $("#FIELD_TO_HOUSING").val(); //Корпус
        query.toPorch = $("#FIELD_TO_PORCH").val(); //Подъезд

        query.toLat = taxi.ordering.toLat + ''; //Координты
        query.toLon = taxi.ordering.toLon + ''; //Координты

        query.phone = $("#FIELD_TEL").val(); //Телефон        

        //Время предзаказа
        if (taxi.ordering.useNewTemplate()) {
            query.priorTime = $('.order-taxi-when-date input').val();
        } else {
            query.priorTime = $('label[for=FIELD_DATA] ~ .controls input').val();
        }
        query.carType = $('#tariff_travel option:selected').text(); //Тип авто - строкой - "Бизнес"
        //query.carGroupId = $('#tariff_travel option:selected').val(); //Тип авто - ИД группы "Бизнес" - 22 например
        query.clientName = $("#FIELD_FIO").val(); //ФИО клиента

        //query.comment = "[" + $("#cost_order").text() + "][" + $("div.control-group.rez p:first-child").text() + "]" + $("#FIELD_COMM").val(); //Комментарий
        query.comment = $("#FIELD_COMM").val();

        query.tariffGroupId = $('#tariff_travel option:selected').val(); //id тарифа для 373

        // Добавление флагов опций - в комментарий:        
        $('label[for=FIELD_DOP] ~ .controls input[type=checkbox]:checked').each(function(index) {
            query.comment += ' ' + $(this).closest('label').text().toString().trim() + ', ';
        });
        query.customCar = taxi.ordering.customCarInfo; //Строка с описанием машины при ручном выборе

console.log(JSON.stringify(taxi.ordering));
        if (taxi.ordering.customCarId > 0) {
            query.customCarId = taxi.ordering.customCarId; //ИД ручного выбора машины
        } else {
            query.customCarId = $("#FIELD_AUTO_ID").val(); //ИД ручного выбора машины
        }

        return query;
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
     * Отобразить информацию о возникших ошибках на форме или браузере
     * @param {TaxiErrorsInfo} errorsInfo
     * @returns {}
     */
    self.displayErrors = function(errorsInfo) {
        $('.errortext').remove();
        if ($('#myTab').length > 0) {
            $('#myTab').after(errorsInfo.summaryHtml);
        } else {
            $('.order-taxi-inner').eq(0).before(errorsInfo.summaryHtml);
        }
    };
    /**
     * Отобразить текущую информацию о заказе на форме или браузере
     * @param {TaxiOrderInfo} orderInfo
     * @returns {undefined}
     */
    self.displayOrderInfo = function(orderInfo) {
        var label = '';
        if (self.orderIdLabel) {
            label = self.orderIdLabel;
        } else {
            label = self.orderId;
        }
        var data = new TaxiOrderData();
        console.log(data);
        var res = "<div>";
        for (var property in orderInfo) {
            if (orderInfo[property] !== null && orderInfo[property] !== '') {
                console.log(property);
                if (property === 'driverPhotoBase64') {
                    res += "<p>Фотография: <p><img id='driverPhotoId' src='data: image/jpeg;base64," + orderInfo[property] + "'  alt='Фотография'/></p></p>";

                } else if (property === 'cost') {
                    res += "<p>Стоимость: " + orderInfo['cost'] + " " + orderInfo['costCurrency'] + "</p>";

                }
                else if (property === 'carTime') {
                    res += "<p>Машина подъедет через: " + orderInfo['carTime'] + " мин." + "</p>";

                }

                else if (data[property]) {
                    res += "<p>" + data[property] + ": " + orderInfo[property] + "</p>";
                }
            }
        }
        res += "</div>";
        $('h4.title_order_info').text('Заказ создан. Информация по заказy.');
        $('#result_order').html(res);
        $('#loading_info').hide();
    };

    /**
     * Отобразить текущую информацию о заказе на форме или браузере
     * @param {string} text
     * @returns {undefined}
     */
    self.displayLoadingInfo = function(text) {
        $('#loading_info').show();
        $('#loading_info font').text(text);
    };

    /**
     * Событие после отмены заказаы
     * @returns {undefined}
     */
    self.afterRejectOrder = function() {
        $('#reject_order').find('font').text('Заказ успешно отменен');
        $('#reject_order').removeClass('active');
        $(this).attr('id', '');
    };

    /**
     * Показ всплывающего окна для ввхода кода
     * @returns {undefined}
     */
    self.showEnterSmsCodeWindow = function() {
        $('#colorboxEnterCode').click();
        self.unlock();
    };

    /**
     * Установить текущий город по умолчанию
     * @param {string} cityName город по умолчанию
     * @returns {TaxiMethod_createOrder}
     */
    self.setDefaultCity = function(cityName) {
        $('#FIELD_CITY_OTKUDA').val(cityName);
        $('#FIELD_CITY_KUDA').val(cityName);
        var url = '/api_integration/include/bitrix/ajax_store_default_city.php';
        $.ajax({
            url: url,
            type: 'post',
            data: {
                defaultCity: cityName
            },
            success: function(response) {
            }
        });
    };

    /**
     * Проверка использования нового шаблона
     * @returns {boolean}
     */
    self.useNewTemplate = function() {
        return $('.detailed-order-taxi-wrapper').length > 0;
    };

    return self;
};



