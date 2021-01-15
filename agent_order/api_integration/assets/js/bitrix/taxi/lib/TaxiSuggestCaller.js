
/*
 * Обработчик автоподсказок
 */

/**
 * Класс для выполнения спецзапроса по подсказке улиц через
 * универсальное апи через клиент Taxi
 */
var TaxiSuggestCaller = function() {
    /**
     * Опции для выполнения POST запроса
     */
    var SuggestCallerOptions = function() {
        return {
            part: '',
            cityGeoCode: '',
            city: ''
        };
    };

    var self = {};
    /**
     * Флаг выключения заросов по улицам и другим автоподсказкам до нашего АПИ
     * - false;
     */
    self.disableApi = false;

    self._lastResponse = null;
    self.callback = function() {
    };

    self.optionsObject = new SuggestCallerOptions();
    /**
     * Создание из пришедсшего "Сырого" массива данных объекта-ответа
     * @param {type} data
     */
    self.createResponseObject = function(data) {
        var variants = [];
        for (var index in data) {
            /*
             * ["geo", 
             "улица Баранова, Ижевск, республика Удмуртская", 
             "Россия, республика Удму...Ижевск, улица Баранова ",
             Object { hl=[3]}
             ]
             */
            var current = data[index];
            if (self.apiMethodName === 'findGeoObjects') {
                variants.push({
                    type: '',
                    label: current.label,
                    fullLabel: current.label,
                    geoData: current,
                    systemData: ''
                });
            } else {
                variants.push({
                    type: '',
                    label: current,
                    fullLabel: current,
                    systemData: ''
                });
            }
        }
        var response = {
            part: data[0],
            variants: variants
        };
        console.log('Suggest responsed');
        return response;
    };

    /**
     * Имя функции АПИ, которая используется при запросах     
     */
    self.apiMethodName = 'findStreets';

    /**
     * Лимит
     */
    self.maxLimit = 500;
    
    /**
     * Внутрений вызов запрос
     */
    self.internalGetRequest = function() {
        var optionsObject = self.optionsObject;
//        console.log(optionsObject.ajaxUrl);
        if (optionsObject.part && optionsObject.part.length > 1) {

            /**
             * При запрете запросов до нашего АПИ, использовать только запрос до яндекс
             * - подсказок
             */
            if (self.disableApi) {
                console.log(self.disableApi);
                taxi.yandexSuggest.search(
                        self.options, self.callback
                        );
                return false;
            }
            var query = new TaxiMethod(self.apiMethodName);
            // передача нужных параметров
            query.params = {
                streetPart: optionsObject.part,
                maxLimit: self.maxLimit,
                city: optionsObject.city
            };
            query.successCallback = function(response) {
//                    console.log(response);
                // обработка "сырого" массива-ответа
                var responseObject = self.createResponseObject(response);
                self._lastResponse = responseObject;
//                    console.log('Использован внутренний подсказчик:');
//                    console.log(responseObject);
                // Если ничего не нашлось, то переключаемся на Яндекс подсказчик
                if (responseObject.variants.length === 0) {
                    // не будем вызывать яндекс так как поиск улиц реализован через api_integration
                   /* taxi.yandexSuggest.search(
                            self.options, self.callback
                            );*/
                    return;
                }
                // выполнение пользовательской функции обработчика ответа со списком улиц
                self.callback(responseObject);
            };
            query.errorCallback = function() {
                console.log('Ошибка при запросе на TaxiClient!');
                /*taxi.yandexSuggest.search(
                        self.options, self.callback
                        );*/
            };
            taxi.taxiClient.executeQuery(query);
        } else {
            return false;
        }
    };
    return {
        part: '',
        // GEO – ключ города
        /**
         * УРЛ куда отправляем запрос
         */
        ajaxUrl: '/include/otaxi/ajax_otaxi_get_streets.php',
        /**
         * Город в текстовом виде
         */
        city: '',
        cityGeoCode: '',
        /**
         * Установить лимит
         * @param {type} maxLimit
         * @returns {undefined}
         */
        setMaxLimit: function(maxLimit) {
            self.maxLimit = maxLimit;
        },
        callback: function(responseObject) {
        },
        /**
         * Выполнить запрос к автоподсказчику по части поискового запроса используя текущие гео настройки поиска
         * @param {object} options - поисковый запроса - {long: 'Россия, город Моск', short: 'Моск'}
         * @param {function} callback - выполнить этот каллбек при успешном ответе
         * @returns {@exp;self@call;internalGetRequest}
         */
        search: function(options, callback) {
            if (typeof (callback) !== 'function') {
                console.log("Bad suggest function callback type! This type must be function(responseObject)!");
            }
            // Инициализируем себя == адаптер запросов и посылаем запрос    
            this.part = options.short;
            this.callback = callback;
            // передаем публичные поля в приватные поля
            self.callback = callback;
            self.options = options;
            self.optionsObject.city = this.city;
            self.optionsObject.part = this.part;
            self.optionsObject.cityGeoCode = this.cityGeoCode;
            self.optionsObject.ajaxUrl = this.ajaxUrl;
            return self.internalGetRequest();
        },
        disableApi: function() {
            self.disableApi = true;
        },
        setApiMethodName: function(apiMethodName)
        {
            if (apiMethodName === 'findStreets' || apiMethodName === 'findGeoObjects') {
                self.apiMethodName = apiMethodName;
            } else {
                var message = "Wrong api method name for taxiSuggestCaller! : " + apiMethodName;
                console.log(message);
                throw message;
            }
        },
        getLastResponse: function() {
            return self._lastResponse;
        }
    };
};
