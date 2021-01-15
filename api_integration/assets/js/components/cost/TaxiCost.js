/* 
 * Примеры использования файлов с js инициализацией
 */

/**
 * Класс для расчета стоимостей
 * @returns {undefined}
 */
function TaxiCost() {
    /**
     * Флаг включения - true
     * @type Boolean
     */
    this.enabled = true;
    /**
     * Информация о последней расчитываемом маршруте
     */
    this.currentRouteData = null;

    /**
     * Некий расчитыватель стоимости
     * - должен иметь метод callCost(routeData) - расчет стоимости в зависимости от пути
     * @type TaxiCost.ByTimeCaller -
     */
//    this.caller = new TaxiCostByTimeCaller();
    this.caller = new TaxiCostDefaultCaller();
}
;

/**
 * Методы 
 */
TaxiCost.prototype = {
    /**
     * Отобразим стоимость на форме или еще где-либо
     * @param {float|integer} cost - стоимость примерная в рублях
     * @returns {undefined}
     */
    display: function(cost) {
        console.log("Called cost - " + cost);
    },
    /**
     * Обновляем стоимость маршрута + отображаем через метод display
     * @param {TaxiCost.RouteData} routeData - данные о маршруте
     * @returns {undefined}
     */
    update: function(routeData) {
        this.currentRouteData = routeData;
        var cost = this.caller.callCost(routeData);
        this.display(cost);
    },
    /**
     * Расчет от пути поездки используя текущий алгоритм
     * @param {TaxiCost.RouteData} routeData - информация о пути
     * @returns {float} - расчитанная стоимость
     */
    callCost: function(routeData) {
        this.currentRouteData = routeData;
        var cost = this.caller.callCost(routeData);
        return cost;
    }
};


/**
 * Стандартный расчет стоимости
 * @returns {undefined}
 */
TaxiCostDefaultCaller = function() {
};
/*
 * Методы
 */
TaxiCostDefaultCaller.prototype = {
    /**
     * Расчет от времени поездки
     * @param {TaxiCost.RouteData} routeData - информация о пути
     * @returns {undefined}
     */
    callCost: function(routeData) {
        //длина маршрута (метры)
        var len = routeData.len;
        // время поездки (минуты)
        var time = routeData.timeInMinutes;
        // цена за километр или за минуту
        var mileage = $('#tariff_travel option:selected').data('mileage'); //PRICE_KM_CITY
        // цена за посадку
        var landing = $('#tariff_travel option:selected').data('landing'); //PRICE_POSADKA_CITY
        // цена за количество включенных километров или за количество включенных минут
        var included = $('#tariff_travel option:selected').data('included'); //KM_VKL_POSAD
        // минимальная цена поездки
        var minpricecity = $('#tariff_travel option:selected').data('minpricecity'); //MIN_PRICE_CITY
        // тип тарифа (0 - по километражу, 1 - по времени)
        var typetariff = $('#tariff_travel option:selected').data('typetariff');
        var cost = 0;
         // расчет цены  для тарифа по километражу 
        if (typetariff == '0' || typetariff == '' || typetariff == undefined) {
            console.log('Po Kilometrazhy');
            if ((len / 1000) > included)
            {
                cost = landing + (len / 1000 - included) * mileage;
            }
            else
            {
                cost = landing;
            }

            if (minpricecity > cost)
            {
                cost = minpricecity;
            }
        }
        // расчет цены  для тарифа по времени
        else if (typetariff === '1') {
             console.log('Po Vremeni');
            if (time > included) {
                cost = landing + (time - included) * mileage;
            } else {
                cost = landing;
            }
            if (minpricecity > cost)
            {
                cost = minpricecity;
            }
        }
        var finalCost = Math.round(cost);
        finalCost = taxi.afterCallCost(finalCost);
        taxi.routesInfoLayout.cost = finalCost;

        return finalCost;
    }
};

/**
 * Информация для обновления стоимости
 * @returns {undefined}
 */
TaxiCostRouteData = function() {
    /**
     * Строка - суммарно - откуда
     */
    this.rawFrom;
    /**
     * Строка - суммарно - куда
     */
    this.rawTo;
    /**
     * Длина в км
     */
    this.len;
    /**
     * Время в мин
     */
    this.timeInMinutes;
    /**
     * Предвартиельное время ( для предзаказов)
     */
    this.priorTime;
};

