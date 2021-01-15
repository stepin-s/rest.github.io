function BitrixOrder() {
}
BitrixOrder.prototype = Object.create(TaxiOrder.prototype);
BitrixOrder.prototype.constructor = BitrixOrder;


BitrixOrder.prototype.findTariffs = function (successCbk, errorCbk) {
  var self = this;

  var method = {
    name: 'findTariffs',
    tryCount: 2,
  };
  self.doRequest(method).then(
          function (result) {
            console.log('ok findTariffs');
            console.log(result);
            //document.getElementById(self.config.html.form.id).dispatchEvent(new CustomEvent('findTariffs', {detail: result}));
            successCbk(result);
          },
          function (response) {
            console.log('err findTariffs');
            console.log(response);
            errorCbk(response);
          }
  );
}

BitrixOrder.prototype.findGeoObjects = function (params, successCbk, errorCbk) {
  var self = this;
  var result = [];

  ymaps.suggest(params.part, {results: self.config.autocompleteMaxLimit}).then(
          function (items) {
            items.forEach(function (item, i) {
              result[i] = {label: item.displayName};
            })
            console.log('ok findGeoObjects');
            console.log(result);
            successCbk(result);
          },
          function (error) {
            console.log('err findGeoObjects');
            console.log(response);
            errorCbk(error);
          });
}

BitrixOrder.prototype.calcCost = function (params, successCbk, errorCbk) {
  ymaps.route([params.from, params.to]).then(function (route) {
    console.log('ok calcCost');
    console.log(route);
    var length = parseInt(route.getLength() / 1000);
    var time = parseInt(route.getTime() / 60);
    var res = {
      time: (time || 0),
      dist: (length || 0),
    }
    successCbk(res);
  }, function (error) {
    errorCbk(error);
  });
}

BitrixOrder.prototype.createOrder = function (formParams, successCbk, errorCbk) {
  var self = this;
  var method = {
    name: 'createOrder',
    params: {params: formParams}
  };
  self.doRequest(method).then(
          function (result) {
            if (!result.hasErrors) {
              console.log('ok createOrder');
              console.log(result);
              successCbk(result);
            }
            else {
              console.log('ok wrong createOrder');
              console.log(result);
              errorCbk({result: false, errors: result.errorsInfo.summaryText});
            }
          },
          function (response) {
            errorCbk({result: false, errors: response.errorsInfo.summaryText});
          }
  );
}