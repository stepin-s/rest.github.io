/* 
 * Добавление и инициализация геокодеров гугл
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
    if (typeof(google) === "undefined" || google === null) {
        return;
    }
    //инициализируем функцию геокодера
    var geocoder = new google.maps.Geocoder();
    return {
        name: 'google',
        find: function(find, limit, callback) {
            limit = limit || 1;
            //если входные данные в find -- массив координат, то преобразуем в массив координат понятных геокодеру
            if (typeof(find) === 'object') {
                find = {'latLng': new google.maps.LatLng(find[0], find[1])};
            } else if (typeof(find) === 'string') { //если входные данные строка, то формируем запрос для обратного геокодирования
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
                    if (typeof(callback) === 'function') {
                        //передаем колбеку обработанный результат поиска
                        callback(geocodes);
                    }
                }
            });
        }
    };
});


