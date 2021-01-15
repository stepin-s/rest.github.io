var FactoryTaxiOrder = function self() {

  return new Promise(function (resolve, reject) {
    self.getParams()
            .then(function (response) {
              self.defineOrderHandler(response)
                      .then(function (response) {
                        self.createOrderHandler(response)
                                .then(function (response) {
                                  resolve(response);
                                })
                      })
            })
  });
}

FactoryTaxiOrder.getParams = function () {
  return new Promise(function (resolve, reject) {
    $.getJSON('/include/taxi_order/index_client.php', {command: 'getParams'})
            .done(function (response) {
              console.log('ok config')
              console.log(response)
              resolve(response);
            })
            .fail(function (response) {
              console.log('err config')
              console.log(response)
            });
  });
}

FactoryTaxiOrder.defineOrderHandler = function (data) {
  var self = this;
  FactoryTaxiOrder.params = data;
  return new Promise(function (resolve, reject) {
    var taxiOrder = null;
    //адаптер
    if (data.params.adapter && data.params.adapter.hasOwnProperty('id')) {
      if (data.params.map) {
        taxiOrder = self.createAdapterMap;
      }
      else {
        taxiOrder = self.createAdapter;
      }
    }
    else {
      //битрикс
      if (data.params.map) {
        taxiOrder = self.createBitrixMap;
      }
      else {
        taxiOrder = self.createBitrix;
      }
    }
    resolve(taxiOrder);
  });
}

FactoryTaxiOrder.createOrderHandler = function (createHandler) {
  return new Promise(function (resolve, reject) {
    createHandler().then(function (response) {
      resolve(response);
    });
  });
}

FactoryTaxiOrder.createAdapter = function () {
  return new Promise(function (resolve, reject) {
    var taxiOrder = new Adapter(FactoryTaxiOrder.params.config);
    FactoryTaxiOrder.params = undefined;
    resolve(taxiOrder);
  });
}

FactoryTaxiOrder.createBitrix = function () {
  return new Promise(function (resolve, reject) {
    var taxiOrder = new Bitrix(FactoryTaxiOrder.params.config);
    FactoryTaxiOrder.params = undefined;
    resolve(taxiOrder);
  });
}

var taxiOrder = {
  ready: function (func) {
    FactoryTaxiOrder().then(function (response) {
      taxiOrder = response;
      taxiOrder.hasFormParams = false;
      func();
    });
  }
}