/* 
 * Добавление и инициализация геокодеров яндекс
 */
/**
 * Создание и инициализации общего объекта - внутреннего (универсального геокодера) для карт яндекс
 * При этом описываем запрос по методу find(searchWords:string, answersLimit:integer, callback:function) после запроса на поиск автокомплита
 * или обработки геокодера
 * @param {string} - имя этого геокодера с коллекции геокодеров
 * @param {type} - конструктор объекта-геокодера, главная функция в котором - это find
 */
taxi.addGeocoder('yandex', new function() {
    //проверяем подгружен ли апи яндекс карт
    if (typeof (ymaps) === "undefined" || ymaps === null) {
        return;
    }
    return {
        // имя нашего внутреннеого геокодера в коллекции
        name: 'yandex',
        // переопределение основной функции, также тут и возможно ограничить территорию выдачи резальтатов по прямоугольной области поиска:
        // http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/geocode.xml
        find: function(find, limit, callback) {
            //Меняем местами координаты для обратного декодирования
//            if (typeof(find) === 'object' && find.length === 2){
//                var tmp = find;
//                find[0] = tmp[1];
//                find[1] = tmp[0];
//            }
            // find = 'Россия, ' + find;
            limit = limit || 1;
            var geocodes = []; //инициализируем пустой массив в результатми
            // Произвольные опции для: например, ограничения выборки геокодера
            // http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/geocode.xml

            // ?? зададим некоторую область поиска вокруг главного города
//            var cityCoors = [53.211463, 56.852775]
//                    , boundedByRadius = 1
//                    ;
//            var from = [cityCoors[0] - boundedByRadius, cityCoors[1] - boundedByRadius]
//                    , to = [cityCoors[0] + boundedByRadius, cityCoors[1] + boundedByRadius];
            // var moscowBounds = [[55.94305795164068, 37.29843782760007], [55.544884368537986, 38.03452181197507]];

            ymapsGeocoderOptions = {
                // Ограничение области поиска: углы прямоугольной области
                // boundedBy: [from, to],
                // boundedBy: moscowBounds,
                // Искать только внутри области, заданной опцией boundedBy. Значение по умолчанию: false        
//                strictBounds: limit !== 1,        
                strictBounds: false,
                results: limit
            };
//            console.log(ymapsGeocoderOptions);
            //отправялем запрос в геокодер
            // http://geocode-maps.yandex.ru/1.x/?geocode=50.58792450886083,36.24359766992159
//            console.log(find);
            ymaps.geocode(find, ymapsGeocoderOptions).then(
                    function(res) {
//                        
                        //console.log(res);
//                       
                        //получаем результат и обрабатываем каждый элемент по отдельности
                        res.geoObjects.each(function(item) {

                            /**
                             * Т.к. не нашлось лучшего способа получить инфу о результатах ищем по возможным путям:
                             * смотреть пример из GET API геокодера:
                             * http://geocode-maps.yandex.ru/1.x/?geocode=Пермь
                             * Тут можно понять где именно лежит инфа 
                             * @returns {object|false} - false - если не будет найден объект геоинфы
                             */
                            function internalFindLocality(item) {
                                var locality = false;
                                var searchLocalityIn = [
                                    'metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.Locality',
                                    'metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.AdministrativeAreaName.Locality',
                                    'metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.SubAdministrativeAreaName',
                                    'metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.Locality',
                                    'metaDataProperty.GeocoderMetaData.AddressDetails.Country.AddressLine',
                                    'metaDataProperty.GeocoderMetaData.AddressDetails.Country.Locality',
                                ];
                                // Координаты в неверном порядке выводились янексом по умолчанию, так что меняем/или не меняем их
                                var coordinates = item.geometry.getCoordinates();
                                var coordinatesString = coordinates[0] + ", " + coordinates[1];
                                for (var searchIndex in searchLocalityIn) {
                                    locality = item.properties.get(searchLocalityIn[searchIndex]);
                                    if (locality) {
                                        if (typeof (locality) === 'string') {
                                            locality.LocalityName = locality;
                                            locality.PseudoStreet = coordinatesString;
                                        }
                                        return locality;
                                    }
                                }
                                // locality = coordinatesString;
                                return locality;
                            }

                            var Locality = internalFindLocality(item);

                            if (!Locality) {
                                return;
                            } //если в этой переменной пусто, то ниче не обрабатываем

                            var dependent = "";//район                            
                            var street = typeof (Locality) === 'string' ? Locality : ""; //улица или координаты \ 
                            var house, housing; //дом, корпус
                            house = housing = "";

                            var city = Locality.LocalityName || ""; //за значение города берем поле LocalityName

                            if (Locality.Thoroughfare) { //обычно тут прячется название улицы и дом
                                street = Locality.Thoroughfare.ThoroughfareName || Locality.Thoroughfare.Premise.PremiseName || Locality.PseudoStreet; //берем название улицы
                                if (Locality.Thoroughfare.Premise) { //смотрим есть ли массив в котором должен быть спрятан номер дома
                                    if (Locality.Thoroughfare.Premise.PremiseNumber) {//если домер дома есть — берем его
                                        house = Locality.Thoroughfare.Premise.PremiseNumber;
                                        housing = house.match(/[к][0-9]+$/i);
                                        if (housing !== null) { //герекспом разбиваем ответ и ищем совпадение в маской [к][0-9]+$
                                            housing = housing[0].match(/[+0-9]+$/i)[0]; //получаем номер корпуса
                                            house = house.match(/^[0-9\/]+/i)[0]; //оставляем только номер дома
                                        }
                                    }
                                }
                            } else if (Locality.DependentLocality) { //если нет массива с улицей, то ищем район

                                if (Locality.DependentLocality.Thoroughfare) { //ищем улицу в районе
                                    street = Locality.DependentLocality.Thoroughfare.ThoroughfareName; //заносим название улицы
                                    // ищем дом в районе
                                    if (Locality.DependentLocality.Thoroughfare.Premise) {
                                        if (Locality.DependentLocality.Thoroughfare.Premise.PremiseNumber) {
                                            house = Locality.DependentLocality.Thoroughfare.Premise.PremiseNumber;
                                        }
                                    }
                                } else {
                                    dependent = Locality.DependentLocality.DependentLocalityName; //берем название района
                                    street = dependent;
                                }
                            } else if (Locality.Premise) { //хз что именно означает этот массив, но в нем тоже может оказаться улица
                                street = Locality.Premise.PremiseName; //берем улицу
                            }

                            //формируем поле ответа геокодера для вывода в автокомплите
                            var c = (city) ? city : "";
                            var d = (dependent) ? ", " + dependent : "";
                            var s = (street) ? ((city) ? ", " + street : street) : "";
                            var h = (house) ? " " + house : "";
                            var hs = (housing) ? " корпус " + housing : "";
                            var label = c + d + s + h + hs;
                            var value = street;
                            /*старый код, раньше вырезалось все регекспом, не совсем надежно*/
                            //var label item.properties.get('name');
                            // var value = item.properties.get('name');
                            /* var elements,remove;
                             elements = remove = null;
                             
                             elements = label.split(',');
                             for (var j = 0; j <= elements.length - 1; j++) {
                             
                             elements[j] = elements[j].replace(/^\s/g,'');
                             
                             if (remove==null){
                             var reg = elements[j].match(/^[0-9]+[\/а-яa-z]*[+0-9]*$/i);
                             if (reg!=null){
                             house = reg[0];
                             housing = house.match(/[к][0-9]+$/i);
                             if (housing!=null){
                             housing = housing[0].match(/[+0-9]+$/i)[0];
                             house = house.match(/^[0-9\/]+/i)[0];
                             }
                             remove = j;
                             
                             }
                             }
                             }
                             if (remove != null){
                             elements.splice(remove,1);
                             elements.splice(0,1);
                             value = elements.join();
                             }*/

                            //записываем результат ответа в массив
                            geocodes.push({
                                point: item.geometry.getCoordinates(),
                                street: street,
                                city: city,
                                label: label,
                                house: house,
                                housing: housing,
                                value: value
                            });
                        }); //end of each
                        //console.log(geocodes);
                        if (typeof (callback) === 'function') {
                            //вызываем колбек и передаем ему результаты обработки
                            callback(geocodes);
                        }
                    },
                    function(/*err*/) {
                    }
            );
        }
    };
});

/* 
 * Добавление и инициализация геокодеров
 */
/**
 * Создание и инициализации общего объекта - внутреннего (универсального геокодера) для карт яндекс
 * При этом описываем запрос по методу find(searchWords:string, answersLimit:integer, callback:function) после запроса на поиск автокомплита
 * или обработки геокодера
 * @param {string} - имя этого геокодера с коллекции геокодеров
 * @param {type} - конструктор объекта-геокодера, главная функция в котором - это find
 */
taxi.addGeocoder('google', new function() {
    //если не подгружжено апи гугла, то расходимся
    if (typeof (google) === "undefined" || google === null) {
        return;
    }
    //инициализируем функцию геокодера
    var geocoder = new google.maps.Geocoder();
    return {
        name: 'google',
        find: function(find, limit, callback) {
            limit = limit || 1;
            //если входные данные в find -- массив координат, то преобразуем в массив координат понятных геокодеру
            if (typeof (find) === 'object') {
                find = {'latLng': new google.maps.LatLng(find[0], find[1])};
            } else if (typeof (find) === 'string') { //если входные данные строка, то формируем запрос для обратного геокодирования
                find = {'address': find};
            }

            //вспомогательная функция для выборки их масства только значения поля long_name при вхождении type в types
            function getType(arr, type) {
                for (var i = arr.length - 1; i >= 0; i--) {
                    if (jQuery.inArray(type, arr[i].types) !== -1) {
                        return arr[i].long_name;
                    }
                }
                return "";
            }
            var geocodes = [];
            var param = {
                //фильтр по региору ru
                'region': 'ru'
            };
            jQuery.extend(param, find);
            geocoder.geocode(param, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    jQuery.each(results, function(i, result) {
                        var city, street, dependent;
                        city = street = dependent = "";
                        var house, housing;
                        house = housing = "";

                        city = getType(result.address_components, 'locality');//получаем город
                        street = getType(result.address_components, 'route'); //получаем улицу
                        if (!street) { //если нет улицы, то берем результат с типом point_of_interest, в нем могу быть например: автовокзал или жд вокзал
                            street = getType(result.address_components, 'point_of_interest');
                        }
                        if (!street) { //если предыдущий поиск ничего не дал, то ищем любое общественное здание
                            street = getType(result.address_components, 'establishment');
                        }
                        if (!street) { //если совсем ничего нет, то берем район
                            dependent = getType(result.address_components, 'administrative_area_level_2');
                        }
                        house = getType(result.address_components, 'street_number'); //номер дома
                        housing = house.match(/[к][0-9]+$/i);
                        if (housing !== null) {//ищем корпус, но пока гугл не умеет делить адреса по корпусам
                            housing = housing[0].match(/[+0-9]+$/i)[0];
                            house = house.match(/^[0-9\/]+/i)[0];
                        }

                        var c = (city) ? city : "";
                        var d = (dependent) ? ", " + dependent : "";
                        var s = (street) ? ((city) ? ", " + street : street) : "";
                        var h = (house) ? " " + house : "";
                        var hs = (housing) ? " корпус " + housing : "";
                        var label = c + d + s + h + hs;
                        var value = street;
                        //старый вариант поиска по регекспу, не особо надежен, но работает
                        // var label = item.properties.get('name');
                        // var value = item.properties.get('name');
                        /* var elements,remove;
                         elements = remove = null;
                         
                         elements = label.split(',');
                         for (var j = 0; j <= elements.length - 1; j++) {
                         elements[j] = elements[j].replace(/^\s/g,'');
                         if (remove==null){
                         var reg = elements[j].match(/^[0-9]+[\/а-яa-z]*[+0-9]*$/i);
                         if (reg!=null&&reg[0].length<6){
                         house = reg[0];
                         housing = house.match(/[к][0-9]+$/i);
                         if (housing!=null){
                         housing = housing[0].match(/[+0-9]+$/i)[0];
                         house = house.match(/^[0-9\/]+/i)[0];
                         }
                         remove = j;
                         }
                         }
                         }
                         if (remove != null){
                         elements.splice(remove,1);
                         }
                         var city = elements[1];
                         elements.splice(1,10); //сносим все что после города
                         elements.reverse(); // разворачиваем
                         value = elements.join(", ");*/

                        var loc = result.geometry.location;
                        //записываем результат в массив
                        geocodes.push({
                            point: [
                                loc.lat(),
                                loc.lng()
                            ],
                            city: city,
                            street: street,
                            label: label,
                            house: house,
                            housing: housing,
                            value: value
                        });
                    });
                    if (typeof (callback) === 'function') {
                        //передаем колбеку обработанный результат поиска
                        callback(geocodes);
                    }
                }
            });
        }
    };
});


