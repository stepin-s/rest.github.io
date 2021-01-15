/* 
 * Добавление карт: гугл
 */
$(document).ready(function() {
    //добавляем карты и геокодеры
    taxi.addMap('google', new function() {
        if (typeof (google) === "undefined" || google === null) {
            return;
        }
        var googleMap = this;
        googleMap.directionsService = null;
        googleMap.route = null;
        googleMap.mypos = null;
        // googleMap.points = [];
        return {
            name: 'google',
            init: function(params) {
                jQuery.extend(googleMap, params);
                //инициализируем карту по координатам города по умолчанию
                googleMap.geocoder(googleMap.city, 1, function(res) {
                    //ставим точку по первому результату поиска
                    var pos = res[0].point;
                    googleMap.map = new google.maps.Map(document.getElementById(googleMap.mapcontainer), {//рисуем в блоке mapcontainer
                        zoom: 11,
                        center: new google.maps.LatLng(pos[0], pos[1]), //ставим точку центра по полученным координатам предворительно преобразовав их в нужный гуглу формат
                        mapTypeId: google.maps.MapTypeId.ROADMAP //выбираем  тип карты
                                /*
                                 ROADMAP – стандартные двухмерные фрагменты Google Карт.
                                 SATELLITE – фрагменты, представленные сделанными со спутника фотографиями.
                                 HYBRID – фотографические фрагменты с наложенным слоем, содержащим наиболее важные объекты (дороги, названия городов).
                                 TERRAIN – фрагменты топографической карты с рельефом местности, высотами и гидрографическими объектами (горы, реки и т. д.).
                                 */
                    });
                });
                //инициализируем объекты
                googleMap.directionsService = new google.maps.DirectionsService(); //возвращает параметры маршрура, т.е. длину пути, время, промежучные точки и т.п.
                googleMap.route = new google.maps.DirectionsRenderer({draggable: false}); //рисует маршрут
                googleMap.mypos = new google.maps.Marker(); //точка "моего" местоположения
            },
            createroute: function() {

                googleMap.route.setMap(null); //удаляем маршрут с карты (не совсем удаляем, переопределяем местоположения рендеринга в null)
                googleMap.mypos.setMap(null); //удаляем точку местоположения
                if (!googleMap.points[0] || typeof (googleMap.points[0].length) === "number") {
                    //если у нас нет точки старта, то выходим
                    return;
                }
                if (!googleMap.points[1] || typeof (googleMap.points[1].length) === "number" && googleMap.points[0]) {
                    //если есть точка старта, но нет точки финиша, то рисуем точку с "моим местоположением" котрую можно двигать мышью
                    var pos = googleMap.points[0].point;
                    pos = new google.maps.LatLng(pos[0], pos[1]);
                    //задаем точу с "моим местоположением"
                    googleMap.mypos = new google.maps.Marker({
                        map: googleMap.map,
                        draggable: true,
                        position: pos
                    });
                    googleMap.mypos.setMap(googleMap.map);//добавляем её на карту
                    googleMap.map.setCenter(pos); //центруем карту по точке

                    //вешаем событие dragend для изменения точки и данных в форме
                    google.maps.event.addListener(googleMap.mypos, "dragend", function() {
                        var point = googleMap.mypos.getPosition(); //получаем координаты где остановился драг точки
                        point = [point.lat(), point.lng()]; //преобразуем координаты в простой массив
                        googleMap.geocoder(point, 1, function(res) {
                            //если геокодер что-то знает об этой точке то задаем её как точку старта и изменяем данные в форме
                            if (res) {
                                googleMap.startpoint(res[0]);
                            }
                        });

                    });
                    return;
                }
                //если у нас есть все необходимые данные то задаем место рендеринга нашего маршрута в карту
                googleMap.route.setMap(googleMap.map);

                //получаем точку старта
                var start = googleMap.points[0].point;
                start = new google.maps.LatLng(start[0], start[1]);
                //получаем точку финиша
                var end = googleMap.points[1].point;
                end = new google.maps.LatLng(end[0], end[1]);
                //формируем запрос к сервису построения маршрута
                var request = {
                    origin: start, //точка старта
                    destination: end, //точка финиша
                    travelMode: google.maps.TravelMode.DRIVING //тип маhршрута
                            /*
                             BICYCLING   маршрут для велосипеда
                             DRIVING     маршрут для автомобиля
                             TRANSIT     маршрут для общественного транспорта
                             WALKING     маршрут для пешехода
                             */
                };
                //строим маршрут
                googleMap.directionsService.route(request, function(result, status) {
                    //если есть результат, то передаем его в рисовальщик маршрута
                    if (status === google.maps.DirectionsStatus.OK) {
                        googleMap.route.setDirections(result);
                    }
                });
                //вешаем событие отвечающее за изменение машртура
                google.maps.event.addListener(googleMap.route, 'directions_changed', function() {
                    //получаем легенду маршрута
                    var leg = googleMap.route.directions.routes[0].legs[0];
                    googleMap.routeinfo({
                        length: leg.distance.value, // Длина маршрута
                        time: leg.duration.value// Время маршрута с пробками
                    });
                    return;

                });
            },
            setPoints: function(points) {
                googleMap.points = points;
                this.createroute();
            },
            mypos: function(loc, callback) {
                var pos = [loc.coords.latitude, loc.coords.longitude];
                // var mymap = this;
                googleMap.geocoder(pos, 1, function(res) {
                    callback(); //вызываем колбек скрывающий иконку поиска местоположения и возвращающий кнопку поиска
                    if (!res) {
                        return;
                    } //если нет результата то выходим
                    googleMap.startpoint(res[0]);//задаем точку по полученным координатам
                    var pos = res[0].point;
                    var latlng = new google.maps.LatLng(pos[0], pos[1]);
                    //задаем зум к нашей точке по принципу: максимально возможный зум для этой области минус 3
                    var maxZoomService = new google.maps.MaxZoomService();
                    maxZoomService.getMaxZoomAtLatLng(latlng, function(response) {
                        if (response.status === google.maps.MaxZoomStatus.OK) {
                            googleMap.map.setZoom(response.zoom - 3);
                        }
                    });
                    //центруем карту
                    googleMap.map.setCenter(latlng);
                });
            }
        };
    });

});



