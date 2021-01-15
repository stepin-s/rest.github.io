/*
 * Инициализация объекта для работы с картой
 */

window["taxi"] = new function() {
    var self = this;
    // приватные переменные
    self.points = [null, null];
    self.city = '';

    //приватные функции
    self.geocoder = function() {
    };
    self.map = function() {
    };
    self.routeinfo = function() {
    };
    self.geocoders = new Collection();
    self.maps = new Collection();

    self.selectGeocoder = function(name) {
        //выбираем геокодер из списка добавленных
        self.geocoder = self.geocoders.get(name).fn;

        return self;
    };
    self.setCity = function(city) {
        /*
         задаем город по умолчанию
         используется только при инициализции карты чтоб спозиционировать в нужном городе
         */
        self.city = city;
        return self;
    };

    self.startInput = function(fn) {
        //привязываем функцию-обработчик вывода значения точки старта для вывода в форму
        if (typeof(fn) === 'function') {
            self.startInput = fn;
        }
        return self.startInput;
    };
    self.endInput = function(fn) {
        //привязываем функцию-обработчик вывода значения точки финиша для вывода в форму
        if (typeof(fn) === 'function') {
            self.endInput = fn;
        }
        return self.endInput;
    };


    return {
        maps: function() {
            return self.maps;
        },
        map: function() {
            return self.maps.first();
        },
        startpoint: function(point, skip) { //задаем точку старта
            /*
             point - точка полученая от геокодера
             skip - строка со списком полей, разделенных через зяпятую,  которые следует исключить при обработке вывода в форму
             */
            if (typeof(point) === 'object') {
                self.points[0] = point;
                self.map.setPoints(self.points); //задаем точки (внутри setPoints вызвыается рендеринг маршрута)
                skip = skip || "";
                self.startInput(point, skip);//вызываем обработчик вывода точки в форму и передаем точку и поля котоные не нужно изменять
            }
            return self.points[0];
        },
        findpoints: function() {
            return self.points;
        },
        endpoint: function(point, skip) {
            //аналогично предыдущей функции, только для точки финиша
            if (typeof(point) === 'object') {
                self.points[1] = point;
                self.map.setPoints(self.points);
                skip = skip || "";
                self.endInput(point, skip);
            }
            return self.points[1];
        },
        addGeocoder: function(name, fn) {
            //добавляем геокодер в коллекцию геокодеров
            self.geocoders.add({
                name: name,
                fn: fn
            });
            return self;
        },
        geocoder: function(find, limit, callback) {
            //публичный доступ к геокодеру для работы автокомплита
            return self.geocoder.find(find, limit, callback);
        },
        mypos: function(loc, callback) {
            //публичная функция, ставит точку старта по loc и передает callback
            return self.map.mypos(loc, callback);
        },
        
         mypos2: function(loc, callback) {
            //публичная функция, ставит точку старта по loc и передает callback
            return self.map.mypos2(loc, callback);
        },
        addMap: function(name, fn) {
            //добавляем карту в коллекцию карт
            self.maps.add({
                name: name,
                fn: fn
            });
            return self;
        },
        selectMap: function(name) {
            /*
             выбираем карту
             в принципе можно сделать её приватной,
             но оставлю публичной на случай если кому-то из заказчиков
             захочется иметь возможность на лету её поменять
             
             для при уже после taxi.init({}) достаточно вызвать:
             taxi.selectMap('google')
             или
             taxi.selectMap('yandex')
             */
            self.map = self.maps.get(name).fn;
            //кешируем объект для передачи публичных методов
            var taxiObj = this;
            self.map.init({
                geocoder: self.geocoder.find,
                city: self.city,
                mapcontainer: self.mapcontainer,
                points: self.points,
                startpoint: function(res) {                    
                    taxiObj.startpoint(res);
                },
                endpoint: function(res) {                    
                    taxiObj.endpoint(res);
                },
                //т.к. следующие функции у нас приватные и недоступны извне, то явно передаем их
                startInput: function(res) {
                    self.startInput(res);
                },
                endInput: function(res) {
                    self.endInput(res);
                },
                routeinfo: function(res) {
                    self.routeinfo(res);
                }
            });
            return self;
        },
        init: function(param) {
            /*
             добавляем наши обработчики из init.js
             */
            self.routeinfo = param.routeinfo; //обработчик расстояния
            self.startInput = param.startInput; //обработчик инпутов точки старта
            self.endInput = param.endInput; //обработчик ипнутов точки финиша

            self.setCity(param.city); //задаем город поумолчанию для карты
            self.selectGeocoder(param.geocoder); //выбираем и инициализируем геокодер
            self.mapcontainer = param.mapcontainer; //задаем id контейнера карты
            this.selectMap(param.map);

        },
        wayPointsData: []
    };
};

// ? создаем рабочий основной объект
var taxi = window["taxi"];
