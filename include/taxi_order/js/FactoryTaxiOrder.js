var FactoryTaxiOrder = function self() {

    return new Promise(function (resolve, reject) {
        self.init()
            .then(function (response) {
                var config = response.config;
                self.defineOrderHandler(response.params)
                    .then(function (response) {
                        self.createOrderHandler(response, config)
                            .then(function (response) {
                                resolve(response);
                            })
                    })
            })
    });
}

FactoryTaxiOrder.loadJsMap = function (jsMap) {
    function load(jsMap, ready) {
        var script = document.createElement('script');
        script.src = jsMap[0];
        document.head.appendChild(script);
        script.onload = function () {
            jsMap.splice(0, 1);
            if (jsMap.length) {
                load(jsMap, ready);
            }
            else {
                ready();
            }
        }
    }

    return new Promise(function (resolve, reject) {
        load(jsMap, resolve);
    });
}

FactoryTaxiOrder.init = function () {
    var self = this;
    return new Promise(function (resolve, reject) {
        $.getJSON('/include/taxi_order/index_client.php', {command: 'init'})
            .done(function (response) {
                console.log('ok init')
                console.log(response)
                self.loadJsMap(response.js).then(function () {
                    delete response.js;
                    resolve(response);
                })
            })
            .fail(function (response) {
                console.log('err config')
                console.log(response)
            });
    });
}

FactoryTaxiOrder.defineOrderHandler = function (params) {
    var self = this;
    return new Promise(function (resolve, reject) {
        if (params.map) {
            var taxiOrder = self.createOrderMap;
        }
        else {
            var taxiOrder = self.createOrder;
        }
        resolve(taxiOrder);
    });
}

FactoryTaxiOrder.createOrderHandler = function (handler, config) {
    return new Promise(function (resolve, reject) {
        handler(config).then(function (response) {
            resolve(response);
        });
    });
}

FactoryTaxiOrder.createOrder = function (config) {
    return new Promise(function (resolve, reject) {
        var taxiOrder = new Order(config);
        resolve(taxiOrder);
    });
}

FactoryTaxiOrder.createOrderMap = function (config) {
    return new Promise(function (resolve, reject) {
        var taxiOrder = new OrderMap(config);
        resolve(taxiOrder);
    });
}

var taxiOrder = {
    ready: function (func) {
        FactoryTaxiOrder().then(function (response) {
            taxiOrder = response;
            taxiOrder.hasFormParams = false;
            //func(taxiOrder);
        });
    }
}