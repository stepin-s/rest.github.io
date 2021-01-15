function OrderMap(config) {
    this.config = config;
    this.order = {};
    return this;
}

OrderMap.prototype = Object.create(OrderCommon.prototype);
OrderMap.prototype.constructor = OrderMap;

OrderMap.prototype.loadMapFiles = function () {
    return new Promise(function (resolve, reject) {
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.type = 'text/css';
        link.href = '/include/taxi_order/adapter/css/leaflet.css';
        document.head.appendChild(link);
        link.onload = function () {
            var script = document.createElement('script');
            script.src = '/include/taxi_order/adapter/js/libs.js';
            document.head.appendChild(script);
            script.onload = function () {
                resolve();
            }
        }
    });
}

OrderMap.prototype.initMap = function (successCbk, errorCbk) {
    var self = this;

    self.loadMapFiles().then(
        function () {
            var city = {lat: 56.0000, lon: 92.8201};
            var geoservice = window.geoservice = 'yandex';

            var map = new L.Map(self.config.html.form.elements.id.map, {
                zoomAnimation: false,
                scrollWheelZoom: false
            });

            L.Icon.Default.imagePath = '/include/taxi_order/adapter/images/';

            var aIcon = L.icon({
                iconUrl: self.config.map.mapAIconSrc,
                iconSize: self.config.map.mapAIconSize,
                iconAnchor: self.config.map.mapAIconAnchor,
            });

            var bIcon = L.icon({
                iconUrl: self.config.map.mapBIconSrc,
                iconSize: self.config.map.mapBIconSize,
                iconAnchor: self.config.map.mapBIconAnchor,
            });

            var mapLine = {
                styles: self.config.map.mapLineStyle,
                missingRouteStyles: self.config.map.mapMissingLineStyle
            };

            map.on('load', function () {
                map.routeControl = L.Routing.control({
                    fitSelectedRoutes: true, createMarker: function (i, wp) {
                        if (i == 0) var marker = L.marker(wp.latLng, {icon: aIcon});
                        else var marker = L.marker(wp.latLng, {icon: bIcon});
                        return marker;
                    }, lineOptions: mapLine
                });
            });

            map.setView([city.lat, city.lon], self.config.map.zoom);

            var mapLayer = null;
            if (geoservice == 'yandex') mapLayer = new L.Yandex();
            if (geoservice == 'google') mapLayer = new L.Google('ROADMAP');
            if (geoservice == 'osm') mapLayer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
            if (geoservice == 'bing') {
                var imagerySet = "AerialWithLabels";
                mapLayer = new L.BingLayer("LfO3DMI9S6GnXD7d0WGs~bq2DRVkmIAzSOFdodzZLvw~Arx8dclDxmZA0Y38tHIJlJfnMbGq5GXeYmrGOUIbS2VLFzRKCK0Yv_bAl6oe-DOc", {type: imagerySet});
            }
            map.addLayer(mapLayer);

            self.orderMap = map;
            successCbk(map);
        }
    );
}

OrderMap.prototype.drawRoute = function(direction, value) {
    var self = this;
    self.order[direction] = {}
    self.order[direction].address = {};

    ymaps.geocode(value, {results: 1}).then(function(res) {
        var coords = res.geoObjects.get(0).geometry.getCoordinates();
        self.order[direction].address.location[0] = ''+coords[0];
        self.order[direction].address.location[1] = ''+coords[0];

        taxiOrder.orderMap.routeControl.setWaypoints([
            L.latLng(coords[0], corrds[1])
        ]);

        taxiOrder.orderMap.routeControl.addTo(taxiOrder.orderMap);
				taxiOrder.orderMap.panTo(coords);

        console.log(self.order)
    });
/*
    if (this.order.from && this.order.to) {
        this.orderMap.routeControl.setWaypoints([
            L.latLng(order.from.address.location[0], order.from.address.location[1]),
            L.latLng(order.to.address.location[0], order.to.address.location[1])
        ]);
    }
    */
}

OrderMap.prototype.findCars = function (successCbk, errorCbk) {
    var self = this;

    var method = {
        name: 'findCars'
    };
    self.doRequest(method).then(
        function (result) {
            console.log('ok findCars');
            console.log(result);
            successCbk(result);
        },
        function (response) {
            console.log('err findCars');
            console.log(response);
            errorCbk(response);
        }
    );

}