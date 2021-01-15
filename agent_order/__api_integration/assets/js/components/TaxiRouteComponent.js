/*
 * Слой для работы с маршрутами 
 */

/**
 * Слой для работы с маршрутами
 */
function TaxiRouteComponent() {
    var self = {};

    self._from = null;
    self._to = null;

    self._hotNeed = {
        from: false,
        to: false
    };
    self._geoData = {
        from: null,
        to: null
    };

    /*
     * GETTERS AND SETTERS
     */

    self.setFrom = function(value) {
        self._from = value;
        self._hotNeed.from = true;
    };
    self.setTo = function(value) {
        console.log('TOOO');
        self._to = value;
        self._hotNeed.to = true;
    };
    self.getTo = function() {
        return self._to;
    };
    self.getFrom = function() {
        return self._from;
    };
    /**
     * Необходимо ли обновить точку маршрута на карте 
     * сейчас - флаг только один раз 
     * @param {boolean} isFrom
     * @returns {boolean}
     */
    self.hotNeedUpdate = function(isFrom) {
        if (isFrom) {
            return self._hotNeed.from;
        } else {
            return self._hotNeed.to;
        }
    };

    /**
     * Вызвать после обновление через этот компонент точки на маршруте
     * @param {boolean} isFrom
     */
    self.resetHotNeedUpdate = function(isFrom) {
        console.log('resetHotNeedUpdate');
        if (isFrom) {
            self._hotNeed.from = false;
        } else {
            self._hotNeed.to = false;
        }
    };

    /*
     * EVENTS
     */

    /**
     * Событие возникает при выборе геообъекта из автокомплита, чтобы задать начальную,
     * либо конечную точку маршрута
     * @param {object} geoData
     * @param {boolean} isFrom
     * @returns {}
     */
    self.onGeoObjectSelect = function(geoData, isFrom)
    {
        if (geoData
                && typeof (geoData.address) !== 'undefined'
                && typeof (geoData.address.location) !== 'undefined'
                && typeof (geoData.address.location.lat) !== 'undefined'
                && geoData.address.location.lat > 0
                && geoData.address.location.lon > 0
                ) {

            if (isFrom) {
                self.setFrom(geoData.address.location);
                self._geoData.from = geoData;
            } else {
                self.setTo(geoData.address.location);
                self._geoData.to = geoData;
            }
        } else {
            self.resetHotNeedUpdate(isFrom);
            if (isFrom) {
                self.setFrom(null);
                self._geoData.from = null;
                self._hotNeed.from = false;
            } else {
                self.setTo(null);
                self._geoData.to = null;
                self._hotNeed.to = false;
            }
        }
    };

    /*
     * METHODS
     */

    self._createGeoLabel = function(isFrom)
    {
        var res = '';
        if (isFrom) {
            if (self._geoData.from) {
                return self._geoData.from.label;
            }
        } else {
            if (self._geoData.to) {
                return self._geoData.to.label;
            }
        }
        return res;
    };
    self._extractGeoPoint = function(obj) {
        return [obj.lat, obj.lon];
    };
    self._createGeoPoint = function(isFrom)
    {
        if (isFrom) {
            if (self._from) {
                return self._extractGeoPoint(self._from);
            }
        } else {
            if (self._to) {
                return self._extractGeoPoint(self._to);
            }
        }
        return null;
    };

    /**
     * Создание псевдоточки для старого построителя маршрута
     * @param {boolean} isFrom - это точка "Откуда" ?
     * @returns {object}
     */
    self.createTaxiRoutePoint = function(isFrom) {
        var point = {
            changed: true,
            city: "",
            house: "",
            housing: "",
            label: "Ленинградская область, Всеволожск",
            point: [60.019041, 30.645577],
            street: "Ленинградская область, Всеволожск",
            value: "Ленинградская область, Всеволожск"
        };
        point.label = point.street = point.value = self._createGeoLabel(isFrom);
        point.point = self._createGeoPoint(isFrom);
        if (isFrom){
            point.geoData = self._geoData.from;
        } else {
            point.geoData = self._geoData.to;
        }
        console.log(point);
        return point;
    };



    return self;
}
;