/* 
 * Добавление карт: яндекс
 */

$(document).ready(function() {
    var city = $('#cityName').text();
    if (!(city.indexOf('Выберите город') + 1)) {
      window.city = city;
		}
		
    /**
     * Цвет пути на карте
     */
    taxi.yandexRoutesStrokeColor = '0000ffee';

    /**
     * Прозрачность пути на карте
     */
    taxi.yandexRoutesOpacity = 0.9;

    //добавляем карты и геокодеры
    taxi.addMap('yandex', new function() {
        if (typeof (ymaps) === "undefined" || ymaps === null) {
            //если обьект ymaps не инициализирован, то мы не сможем дальше строить карту
            return;
        }
        var yandexMap = this; //закешируем объект

        window.yandexMap = this;

        yandexMap.route = null;
        yandexMap.point_source = null;
        yandexMap.mypos = null;
        yandexMap.points = null;
        yandexMap.lastRoute = null;
        taxi.yandexMap = yandexMap;
        function ymapsinit() {
            yandexMap.geocoder(yandexMap.city, 1, function(res) {
                /*
                 делаем запрос к геокодеру по названию нашего города по умолчанию
                 и ставим карту по центу полученных координат
                 */
                var pos = res[0].point;

                if (typeof (taxi.yandexMapBehaviors) === 'undefined') {
                    taxi.yandexMapBehaviors = ['default', 'multiTouch'];
                }
                yandexMap.map = new ymaps.Map(yandexMap.mapcontainer, {//передаем id блока, куда вставлять карту
                    //задаем центр
                    center: [pos[0], pos[1]],
                    //ставим zoom = 11
                    zoom: 11,
                    //добаялем скролл колесом мыши и жесты мультитача для зума и перемещения карты
                    //                    behaviors: ['default', 'scrollZoom', 'multiTouch'],
                    //  А Нихера - глюки с выключением, лучше потом включим нахер!))
                    behaviors: taxi.yandexMapBehaviors,
                    load: ['package.traffic']
                });
                yandexMap.map.controls
                        .add('zoomControl')
                        .add('typeSelector');
                /* 
                 * добавление элемента управления "Пробки" на карту
                 */
                var traffic = new ymaps.control.TrafficControl();
                yandexMap.map.controls.add(traffic);
//            traffic.state.set('shown', true);
            });

//---------------------------------------------------------------------------------------------------------
            var source = window.source + '';
            if (source.length > 0) {
                ymaps.geocode(window.order_city + ',' + window.source, {results: 1}).then(function(res) {
                    var firstGeoObject = res.geoObjects.get(0);
                    if (firstGeoObject && firstGeoObject.length > 0) {
                        yandexMap.map.geoObjects.add(
                                new ymaps.Placemark(
                                        firstGeoObject.geometry.getCoordinates()
                                        , {balloonContentBody: 'Адрес посадки', hintContent: 'Адрес посадки'}
                                , {iconImageHref: '/bitrix/templates/taxi_yellow/i/ico-taxi.png', iconImageSize: [41, 36]}
                                ));
                    }
                });
            }

            if (window.order && parseInt(window.crew, 10) > 0)
                var fp = window.crew;
            else
                var fp = false;

            /**
             * Запуск процесса обновления машин на карте
             */
            $(document).ready(function() {
                setTimeout(function() {
                    taxi.updateCarMarks(yandexMap);
                    $(document).trigger('taxi_map_afterInit');
                }, 2000);
            });

//---------------------------------------------------------------------------------------------------------
        }

        /**
         * Создание строки с описанием позыного
         * @param {type} carInfo
         * @returns 
         */
        taxi.createCarCrewOrId = function(carInfo) {
            var carIdOrCrewInfo = '';
            if (carInfo.crewId) {
                carIdOrCrewInfo = carIdOrCrewInfo + 'Позывной: ' + carInfo.crewId + "<br />";
            }
            return carIdOrCrewInfo;
        };

        /**
         * Создание на карте Яндекс маркера, обозначающего машину
         * @param {TaxiCarInfo} carInfo - структура данных о машине
         * @returns {undefined}- объекта YandexMap - маркер
         */
        taxi.createYandexMapCarMark = function(carInfo) {
//            if (carInfo.number === '') {
//                carInfo.number = '---';
//            }
            var carNumberInfo = '';
            if (carInfo.number) {
                carNumberInfo = 'Гос.номер: ' + carInfo.number + '<br>';
            }
            var carProductionYear='';
            if (carInfo.productionYear) {
                carProductionYear= 'Год выпуска: ' + carInfo.productionYear + '<br>';
            }
            //строка с информацией о позывном
            var carIdOrCrewInfo = taxi.createCarCrewOrId(carInfo);

            carInfo.label = carInfo.description + ' ' + carInfo.color.toLowerCase() + ' ' + carNumberInfo + carIdOrCrewInfo + carProductionYear;

            if (carInfo.isFree) {
                /* Если вместо позывного мы имеем только лишь ИД, то берем его в качестве идентификатора машины */
                if (carInfo.crewId && carInfo.id) {
                    carIdOrCrewInfo = carIdOrCrewInfo + "";
                }
                var carMark = new ymaps.Placemark([carInfo.lat, carInfo.lon], {
                    balloonContentBody: carInfo.description + " <em>" +  carInfo.color.toLowerCase() + "</em>",
                    balloonContentFooter: carIdOrCrewInfo + carNumberInfo + carProductionYear
                            + '<a href="#" onclick="taxi.selectCar(' + carInfo.id + ', \'' + carInfo.label
                            + '\', $(this)); return false;">Выбрать авто</a>',
                    hintContent: carInfo.description  +  " <em>"  + carInfo.color.toLowerCase() + "</em>"
                }, {iconImageHref: '/bitrix/templates/taxi_yellow/i/ico-taxi.png', iconImageSize: [41, 36]});
            } else {
                var carMark = new ymaps.Placemark([carInfo.lat, carInfo.lon], {
                    hintContent: carInfo.description + " <em>" + carInfo.color.toLowerCase() + "</em>"
                },
                {iconImageHref: '/bitrix/templates/taxi_yellow/i/ico-taxi-red.png', iconImageSize: [41, 36]}
                );

            }

            carMark.crew_code = carInfo.id;
            carMark.label = carInfo.label;
            carMark.carId = carInfo.id;
            return carMark;
        };

        /**
         * Выбор авто
         * @param {integer} carId
         * @param {string} label
         * @parm {jQuery} senderA - ссылка Выбрать авто
         * @returns {undefined}
         */
        taxi.selectCar = function(carId, label, senderA) {
            $('#FIELD_AUTO').val('Хочу заказать машину: ' + label);
            $('#FIELD_AUTO_ID').val('' + carId);

            taxi.ordering.selectCustomCar(carId, label);
            //taxi.ordering.carLabel = label;

            if (senderA) {
                var oldOnClick = senderA.attr('onclick');
                var oldHtml = senderA.html();
                senderA.attr('onclick', 'return false;');
                senderA.html(' - Выбрано авто ' + label);
                /* восстановление кнопки через некоторое время */
                setTimeout(function() {
                    senderA.attr('onclick', oldOnClick);
                    senderA.html(oldHtml);
                }, 1000);
            }
        };

        /**
         * Удалить \ очистить машины на карте
         * @returns {undefined}
         */
        taxi._clearCarsOnMap = function() {
            if (!yandexMap.map) {
                return;
            }
            for (var carMark in yandexMap.carMarks) {
                if (yandexMap.carMarks[carMark] === null) {
                    continue;
                }
                yandexMap.map.geoObjects.remove(yandexMap.carMarks[carMark]);
            }
        };

        /**
         * Отобразить на карте машины из информации о машинах
         * @param {type} carsInfo
         * @returns {undefined}
         */
        taxi._drawCarsOnMap = function(carsInfo) {
            if (!yandexMap.map) {
                return;
            }
            taxi._clearCarsOnMap();
            var marks = [];
            for (var key in carsInfo) {
                var carInfo = carsInfo[key];
                var carMark = taxi.createYandexMapCarMark(carInfo);
                marks[carInfo.id] = carMark;

                /*
                 * Пример навешиваения события на клик на точку
                 *     carMark.events.add('click', function(e) {});
                 */
                if (yandexMap && typeof (yandexMap) !== 'undefined') {
                    yandexMap.map.geoObjects.add(marks[carInfo.id]);
                }
            }
            yandexMap.carMarks = marks;
            taxi.startNextUpdateCarMarks();
        };

        /**
         * Добавление балуна с точкой посадки
         * @returns {undefined}
         */
        taxi._addFromPointMark = function() {
            if (!yandexMap.map) {
                return;
            }
            var fromCoords = taxi.ordering.getFromPoint();
            if (fromCoords) {
                yandexMap.map.geoObjects.add(
                        new ymaps.Placemark(
                                fromCoords
                                , {balloonContentBody: 'Адрес посадки', hintContent: 'Адрес посадки'}
                        , {iconImageHref: '/bitrix/templates/taxi_yellow/i/ico-taxi.png', iconImageSize: [41, 36]}
                        ));
            }
        };

        /**
         * Обновление и добавление точек водителей на карте с запросом через API
         * Используем запрос к клиенту - получим информацию о машинах
         * @param {type} yandexMap
         * @returns {undefined}
         */
        taxi.updateCarMarks = function(yandexMap) {
            var method = new TaxiMethod();
            if (!taxi.ordering.isOnFinalState()) {
                method.tryCount = 1;
                method.name = 'findCars';
                method.successCallback = function(data) {
            // Рисуем/перерисовывем авто на карте только если массив не пуст
                     if (data[0]){
                        taxi._drawCarsOnMap(data);
                    } else {
                        taxi.startNextUpdateCarMarks();
                    }
                };
            } else {
                console.log('finding 1 car...');
                taxi._addFromPointMark();
                method.name = 'getCarInfo';
                method.successCallback = function(carInfo) {
                    var data = [];
                    data.push(carInfo);
                    taxi._drawCarsOnMap(data);
                };
                method.params = {
                    carId: taxi.ordering.carId
                };
            }
            method.errorCallback = function() {
                taxi.startNextUpdateCarMarks();
            };
            taxi.taxiClient.executeQuery(method);
        };

        /*
         * Запуск с задержкой
         * последовательного обновления машин на карте
         */
        taxi.startNextUpdateCarMarks = function() {
            /*
             * Обновляем свободные машины на карте
             */
            if (taxi.ordering.getUpdateCarsInterval() > 0) {
                setTimeout(function() {
                    taxi.updateCarMarks(yandexMap);
                }, taxi.ordering.getUpdateCarsInterval());
            } else {
                /*
                 * Пустой цикл обновления
                 * @returns {undefined}
                 */
                setTimeout(function() {
                    taxi.startNextUpdateCarMarks();
                }, 3000);
            }
        };

        window.yandexMap = yandexMap;

        $(document).trigger('onTaxiLoad');

//---------------------------------------------------------------------------------------------------------	


        /*function resetroute() {
         }*/
        return {
            name: 'yandex', //возвращаем поле name для поиска по коллекции карт
            init: function(params) {
                jQuery.extend(yandexMap, params);
                ymaps.ready(function() {
                    ymapsinit();
                });

            },
            //строим роутинг|добавлем точки
            createroute: function() {
                //если уже строили, то удаляем с карты
//            if (yandexMap.route) {
//                yandexMap.map.geoObjects.remove(yandexMap.route);
//            }
                //если точка начального местоположения уже есть на карте, то удаляем её
                if (yandexMap.mypos) {
                    yandexMap.map.geoObjects.remove(yandexMap.mypos);
                }
                yandexMap.mypos = null;
                yandexMap.route = null;
                yandexMap.source_pos = null;

                if (!yandexMap.points[0] || typeof (yandexMap.points[0].length) === "number") {
                    //если не задана точка старта, то расходимся
                    return;
                }
                if (!yandexMap.points[1] || typeof (yandexMap.points[1].length) === "number" && yandexMap.points[0]) {
                    /*
                     если задана точка старта, но нет точки финиша,
                     то ставим точку и вешаем на неё событие переноса по карте
                     */

                    yandexMap.mypos = new ymaps.Placemark(yandexMap.points[0].point, {//инициализируем точку
                        iconContent: ""
                    }, {
                        draggable: true,
                        iconImageHref: '/bitrix/templates/taxi_yellow/i/A.png',
                        iconImageSize: [41, 36]
                    });
                    yandexMap.mypos.events.add('dragend', function() {
                        //внешаем событие окончания переноса
                        yandexMap.geocoder(yandexMap.mypos.geometry.getCoordinates(), 1, function(res) {
                            //если по полученным резуальтатам есть ответ геокодера, то задаем новую точку старта из первого результата
                            if (res) {
                                // self.startInput(res[0]);
                                /*
                                 * Запись точки в поле с улицей
                                 * и т.д.
                                 */
                                yandexMap.startpoint(res[0]);
                                // self.points[0].label= res[0]['label'];
                                yandexMap.points[0].point = yandexMap.mypos.geometry.getCoordinates();
                            }
                        });
                    });
                    yandexMap.map.geoObjects.add(yandexMap.mypos); // добавляем точку на карту
                    yandexMap.map.setCenter(yandexMap.points[0].point); //центруем карту по точке
                    return; //завешраем работу функции
                }

                //сюда мы попадаем только когда заданы обе точки
                var point = [];
                for (var i = 0, l = yandexMap.points.length; i < l; i++) {
                    //получам массив только с координатами
                    point[i] = yandexMap.points[i].point;
                }
                //скармливаем массив с координатами роутеру
                ymaps.route(point, {
                    mapStateAutoApply: true
                }).then(function(router) {
                  /*  router.getPaths().options.set({
                  
                    });*/
                    // Удаление старого пути, если он был

                    //   myRoute && myMap.geoObjects.remove(myRoute);
                    if (yandexMap.lastRoute) {
                        yandexMap.map.geoObjects.remove(yandexMap.lastRoute);
                    }

                    yandexMap.route = router;
                    console.log(yandexMap.route);
                    yandexMap.editor = yandexMap.route.editor;
                    console.log(yandexMap.editor);
                    yandexMap.path = yandexMap.route.path;
                    console.log(yandexMap.path);
                    
                    
                    yandexMap.editor.start({addWayPoints: false}); //запрещаем добавлене точек кликом на карте
                    var editpoint;
                    yandexMap.editor.events
                            .add('routeupdate', function() {
                                /*
                                 у карт яндекса есть действующая на нервы особенность:
                                 после обновления маршрута он выставляет зум так, 
                                 чтобы маршрут умещался в область видимости карты.
                                 если с первого раза при драге точки не попасти в нужную,
                                 то надо заново зумить и двигать
                                 */
                                if (yandexMap.route) {
                                    yandexMap.routeinfo({
                                        length: yandexMap.route.getLength(), // Длина маршрута
                                        time: yandexMap.route.getJamsTime()// Время маршрута с пробками
                                    });
                                }
                            })
                            .add('waypointdragstart', function(e) {
                                

                                console.log('start');
                                var waypoint = e.get('wayPoint');
                                console.log(waypoint);
                                //некрасивый костыль для определения какая сейчас точка двигается
                                editpoint = parseInt(waypoint.properties.get('iconContent'), 10) - 1;
                                // waypoint.options.set('iconContentSize', [0, 0]);

                            })
                            /*
                             * 
                             * @param {type} e
                             * @returns {undefined}
                             */
                            .add('waypointdragend', function(e) {
                                console.log('end');
                                if (typeof (editpoint) === "number") {
                                    var waypoint = e.get('wayPoint');

                                    yandexMap.geocoder(waypoint.geometry.getCoordinates(), 1, function(res) {
                                        // console.log(waypoint.geometry.getCoordinates());
                                        if (res.length > 0) {
                                            switch (editpoint) {
                                                case 0: //если двигали точку с индкесом 0, то меняем координаты точки старта
                                                    yandexMap.startpoint(res[0]);
                                                    break;
                                                case 1: //если индекс был 1, то меняем точку финиша
                                                    yandexMap.endpoint(res[0]);
                                                    break;
                                            }
                                            console.log("res[0].label" + res[0].label);
                                            taxi.wayPointsData[editpoint] = res[0].label;
                                            // waypoint.options.set('iconContentSize', [0, 0]);

                                        } else {
                                            // Если yandexMap.geocoder ничего не нашел на наши координаты                                
                                            taxi.wayPointsData[editpoint] = editpoint + 1 + '';
                                            yandexMap.updateWayPointsText();
                                        }
                                    });
                                }
                            });
                    yandexMap.route.getPaths().options.set({
                        strokeColor: taxi.yandexRoutesStrokeColor, //задаем синий цвет обводки пути в формате rgba, т.е. r = 00, g = 00, b = ff, alpha = ee
                        opacity: taxi.yandexRoutesOpacity //прозрачность пути
                    });
                    yandexMap.editor.events.fire('routeupdate');
                    // сохраним ссылку на прошлый путь для его удаления
                    yandexMap.lastRoute = yandexMap.route;
                    yandexMap.map.geoObjects.add(yandexMap.route);

                    /**
                     * Обновить Подписи к путевым точкам
                     * @returns {undefined}
                     */
                    yandexMap.updateWayPointsText = function() {
                        yandexMap.route.getWayPoints().each(function(item, i) {
                            //  console.log(i);
//                  console.log(item.properties.get('balloonContent'));
//                  console.log(taxi.wayPointsData);
                            if (taxi.wayPointsData.length > i) {
                                var address = taxi.wayPointsData[i];
                            } else {
                                var address = '';
                            }
                            var coordinates = item.geometry.getCoordinates();
                            // Координаты в неверном порядке выводились янексом по умолчанию, так что меняем/или не меняем их
                            var content = coordinates[0] + ", " + coordinates[1];
                            // console.log(address);
                            item.properties.set('balloonContentBody',
                                    '<p>'
                                    + address
                                    + '</p>'
                                    + '<span style="font-size: small">('
//                                + item.properties.get('balloonContent')
                                    + content
                                    + ')</span>');
                            item.properties.set('iconContent', '');
                            if (i == 0) {
                                item.options.set('iconImageHref', '/bitrix/templates/taxi_yellow/i/A.png');
                                item.options.set('iconImageSize', [41, 36]);
                            }
                            if (i == 1) {
                                item.options.set('iconImageHref', '/bitrix/templates/taxi_yellow/i/B.png');
                                item.options.set('iconImageSize', [41, 36]);
                            }

                        });
                    };
                    yandexMap.updateWayPointsText();
//                console.log(yandexMap.map);
//                yandexMap.map.setZoom(yandexMap.map.getZoom() - 1);
                    /*
                     var address = geocode.geoObjects.get(0) &&
                     geocode.geoObjects.get(0).properties.get('balloonContentBody') || '';
                     
                     var distance = Math.round(router.getLength() / 1000),
                     message = '<span>Расстояние: ' + distance + 'км.</span><br/>' +
                     '<span style="font-weight: bold; font-style: italic">Стоимость доставки: %sр.</span>';
                     
                     self._route = router.getPaths();
                     self._route.options.set({ strokeWidth: 5, strokeColor: '0000ffff', opacity: 0.5 });
                     self._map.geoObjects.add(self._route);
                     self._start.properties.set('balloonContentBody', address + message.replace('%s', self.calculate(distance)));
                     self._start.balloon.open();
                     */


                });

            },
            //адает точки и строит маршрут, используется при обновлени точек из инпутов
            setPoints: function(points) {
                yandexMap.points = points;
                this.createroute();
            },
            mypos: function(loc, callback) {
                // var mymap = this;
                var pos = [loc.coords.latitude, loc.coords.longitude];
                yandexMap.geocoder(pos, 1, function(res) {
                    callback(); //вызываем колбек: убираем анимацию преловадера, возвщаем на место иконку "найти меня"
                    if (!res || res.length === 0 || res[0].length === 0) {
                        return;
                    } //если нет результата то выходим
                    yandexMap.startpoint(res[0]); //задаем точку старта
                    ymaps.getZoomRange('yandex#map', res[0].point).then(function(result) {
                        yandexMap.map.setZoom(result[1] - 1); //ставим зум на точку
                    });
                });
            },
            mypos2: function(loc, callback) {
                // var mymap = this;
                var pos = [loc.coords.latitude, loc.coords.longitude];
                yandexMap.geocoder(pos, 1, function(res) {
                    callback(); //вызываем колбек: убираем анимацию преловадера, возвщаем на место иконку "найти меня"
                    if (!res || res.length === 0 || res[0].length === 0) {
                        return;
                    } //если нет результата то выходим
                    yandexMap.endpoint(res[0]); //задаем точку старта
                    ymaps.getZoomRange('yandex#map', res[0].point).then(function(result) {
                        yandexMap.map.setZoom(result[1] - 1); //ставим зум на точку
                    });
                });
            }

        };

    });
});