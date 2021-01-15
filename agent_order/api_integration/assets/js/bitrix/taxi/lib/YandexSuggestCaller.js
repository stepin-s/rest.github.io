/*
 * Класс для выполнения спецзапроса к яндексу
 */

/**
 * Класс для выполнения спецзапроса к яндексу
 * @returns {YandexSuggestCaller.Anonym$3}
 */
var YandexSuggestCaller = function() {
    var self = this;
    self._lastResponse = null;
    self.callback = function() {
    };

    /**
     * Создание из пришедсшего "Сырого" массива данных объекта-ответа
     * @param {type} data
     */
    self.createResponseObject = function(data) {
        if (data.length < 3) {
            console.log("Error in parsing suggest response!");
            return false;
        }
        var variants = [];
        for (var index in data[1]) {
            /*
             * ["geo", 
             "улица Баранова, Ижевск, республика Удмуртская", 
             "Россия, республика Удму...Ижевск, улица Баранова ",
             Object { hl=[3]}
             ]
             */
            var current = data[1][index];
            variants.push({
                type: current[0],
                label: current[1],
                fullLabel: current[2],
                systemData: current[3]
            });
        }
        var response = {
            part: data[0],
            variants: variants
        };
//        console.log('Использован подсказчик Yandex:');
//        console.log(response);
        return response;
    };

    /**
     * Глобальная функция для обработки ответа Yandex Suggest 
     * скроем её имя
     * @param {type} data
     * @returns {undefined}
     */
    window.function_Sj2dk83xZi450_callback = function(data) {
        // обработка "сырого" массива-ответа
        var responseObject = self.createResponseObject(data);
        self._lastResponse = responseObject;
        // вызов текущего переданного каллбека
        self.callback(responseObject);
    };

    /**
     * part — часть слова, Параметр 
     * ll задаёт долготу и широту центра области (в градусах), через кодированный пробел
     * spn — её протяженность (в градусах).
     * Протяженность области задается двумя числами, первое из которых есть разница между максимальной и минимальной долготой, 
     * а второе — между максимальной и минимальной широтой данной области.
     * lang=ru-RU
     * search_type=all
     * fullpath =1 выдает только полные названия, 
     * v=5, видимо, версия API, с др.цифрами не работает
     */
    var SuggestCallerOptions = function() {
        return {
            // Это должна быть объявленная выше глобальная уникальная
            callback: 'function_Sj2dk83xZi450_callback',
            part: '',
            lang: 'ru-RU',
            search_type: 'all',
            // Поиск по области всей России идет по умолчанию
            // Центр области
//                ll: '105.71206938476564,63.056744844350014',
            ll: '64.22769438476561,62.20712366243136',
            // Границы области
//                spn: '165.23437499985945,67.98129092248304',
            spn: '82.61718750000001,32.68027802373878',
            fullpath: '1',
            v: '5'
        };
    };
    self.optionsObject = new SuggestCallerOptions();

    /**
     * Внутрений вызов запрос
     */
    self.internalGetRequest = function() {
        var optionsObject = self.optionsObject;
        if (optionsObject.part && optionsObject.part.length > 1) {
            var url = 'http://suggest-maps.yandex.ru/suggest-geo';

            $.ajax({
                url: url,
                type: 'get',
                data: optionsObject,
                // установим кросс-доменность и специальный вид ответа - запрос будет обработан как код
                dataType: 'jsonp',
                jsonp: "jsonp",
                crossDomain: true,
                success: function(response) {
                    // сюда на самом деле упраление не придет, а будет выполнен код в ответе - т.к.
                    // запрос был кроссдоменным и AJAX не работает как обычно
                    console.log(response);
                }
            });
        } else {
            return false;
        }
    };
    return {
        part: '',
        callback: function(responseObject) {
        },
        // Тип поиска: возможно, что принимает значения: 'all' , 
        search_type: 'all',
        // Поиск по области всей России идет по умолчанию
        // Центр области
        ll: '64.22769438476561,62.20712366243136',
        // Границы области: Протяженность области задается двумя числами, первое из которых есть разница между максимальной и минимальной долготой, 
        // а второе — между максимальной и минимальной широтой данной области. 
        spn: '82.61718750000001,32.68027802373878',
        /**
         * Выполнить запрос к автоподсказчику по части поискового запроса используя текущие гео настройки поиска
         * @param {object} options - поисковый запроса - {long: 'Россия, город Моск', short: 'Моск'}
         * @param {function} callback - выполнить этот каллбек при успешном ответе
         * @returns {@exp;self@call;internalGetRequest}
         */
        search: function(options, callback) {
            if (typeof(callback) !== 'function') {
                console.log("Bad suggest function callback type! This type must be function(responseObject)!");
            }
            // Инициализируем себя == адаптер запросов и посылаем запрос    
            this.part = options.long;
            this.callback = callback;

            // передаем публичные поля в приватные поля
            self.callback = callback;
            self.optionsObject.part = this.part;
            self.optionsObject.search_type = this.search_type;
            self.optionsObject.ll = this.ll;
            self.optionsObject.spn = this.spn;

            return self.internalGetRequest();
        },
        getLastResponse: function() {
            return self._lastResponse;
        }
    };
};

/* Для нормальной работы необходимо создать адаптер запросов через Яндекс */
taxi.yandexSuggest = new YandexSuggestCaller();

/* Установка обработчика по умолчанию через Яндекс */
taxi.suggestCaller = taxi.yandexSuggest;