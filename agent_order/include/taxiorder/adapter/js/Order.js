function Order() {
}
Order.prototype = Object.create(TaxiOrder.prototype);
Order.prototype.constructor = Order;


Order.prototype.findTariffs = function (successCbk, errorCbk) {
    var self = this;

    var method = {
        name: 'findTariffs'
    };
    self.doRequest(method).then(
        function (result) {
            successCbk(result);
        },
        function (response) {
            errorCbk(response);
        }
    );
}

Order.prototype.findGeoObjects = function (params, successCbk, errorCbk) {
    var self = this;

    var method = {
        name: 'findGeoObjects',
        params: {
            maxLimit: params.maxLimit || self.config.request.autoCompleteMaxLimit,
            streetPart: params.part,
            city: params.city
        }
    };
    self.doRequest(method).then(
        function (result) {
            successCbk(result);
        },
        function (response) {
            errorCbk(response);
        }
    );
}

Order.prototype.calcCost = function (params, successCbk, errorCbk) {
    var self = this;
    var method = {
        name: 'callCost',
        params: params
    };
    self.doRequest(method).then(
        function (result) {
            var res = {
                cost: (result.summaryCost || result.summary_cost || 0),
                time: (result.summaryTime || result.summary_time || 0),
                dist: Math.ceil((result.summaryDistance || result.summary_distance || 0))
            };
            successCbk(res);
        },
        function (response) {
            errorCbk(response);
        }
    );
}

Order.prototype.validateParams = function (formParams, successCbk, errorCbk) {
    var self = this;
    var method = {
        name: 'validateCommand',
        params: {
            command: 'createOrder',
            paramsToValidate: formParams
        }
    };
    self.doRequest(method).then(
        function (result) {
            if (!result.hasErrors) {
                successCbk({check: true});
            }
            else {
                $('.vk-order__body--tel .vk-order__address-cols:nth-of-type(2)')
                    .append(result.errorsInfo.summaryHtml)
                    .css("color", "red");
                errorCbk({check: false, errors: result.errorsInfo.summaryHtml});
            }
        },
        function (response) {
            errorCbk(response);
        }
    );
}

Order.prototype.needSendSms = function (phone, successCbk, errorCbk) {
    var self = this;
    var method = {
        name: 'needSendSms',
        params: {
            phone: phone
        }
    };
    self.doRequest(method).then(
        function (result) {
            if (0 == result) {
                successCbk(false);
            }
            else if (1 == result) {
                successCbk(true);
            }
        },
        function (response) {
            errorCbk(response);
        }
    );
}

Order.prototype.sendSms = function (phone, successCbk, errorCbk) {
    var self = this;

    var method = {
        name: 'sendSms',
        params: {
            phone: phone
        }
    }
    self.doRequest(method).then(
        function (result) {
            successCbk(result);
        },
        function (response) {
            if (result.success = false) {
                $('#sms-error')
                    .append(result.text)
                    .css("color", "red");
                $('#input-sms-code').hide();
                $('#button-sms-code').val('Новый заказ').removeAttr("onclick");
                errorCbk({check: false, errors: result.text});    
        }
    });
}

Order.prototype.login = function (phone, smsCode, successCbk, errorCbk) {
    var self = this;

    var method = {
        name: 'login',
        params: {
            phone: phone,
            typeId: '',
            smsCode: smsCode
        }
    };
    self.doRequest(method).then(
        function (result) {
            if (!result.success) {
                errorCbk(result)
            }
            else {
                successCbk(result);
            }
        },
        function (response) {
            errorCbk(response);
        }
    );
}

Order.prototype.createOrder = function (params, successCbk, errorCbk) {
    var self = this;

    var method = {
        name: 'createOrder',
        params: params
    };
    self.doRequest(method).then(
        function (result) {
            successCbk(result);
        },
        function (response) {
            errorCbk(response);
        }
    );
}

Order.prototype.getOrderInfoInterval = function (orderId, successCbk, errorCbk) {
    var self = this;

    var timer = setInterval(function () {
        self.getOrderInfo(orderId,
            function (result) {
                successCbk(result);
                if ('completed' == result.status || 'rejected' == result.status)
                    clearInterval(timer);
            },
            function (response) {
                errorCbk(response);
            }
        );
    }, self.config.request.orderInfoInterval);
}

Order.prototype.getOrderInfo = function (orderId, successCbk, errorCbk) {
    var self = this;

    var method = {
        name: 'getOrderInfo',
        params: {
            orderId: orderId
        }
    };

    self.doRequest(method).then(
        function (result) {
            successCbk(result);
        },
        function (response) {
            errorCbk(response);
        }
    );
}

Order.prototype.rejectOrder = function (orderId, successCbk, errorCbk) {
    var self = this;

    var method = {
        name: 'rejectOrder',
        params: {orderId: orderId}
    };

    self.doRequest(method).then(
        function () {
            successCbk(true);
        },
        function (response) {
            errorCbk(response);
        }
    );
}