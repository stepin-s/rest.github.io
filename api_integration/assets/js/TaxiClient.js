/**
 * Основной объект для работы связи с клиентом PHP
 * @returns {TaxiClient}
 */
TaxiClient = function() {
    var self = {};

    self.enabled = true;
    self.ajaxClientUrl = '/api_integration/index_client.php';
    self.isDevMode = true;
    self.log = function(variable) {
        if (self.isDevMode) {
            console.log(variable);
        }
    };

    /**
     * Выполнение команды/запроса к нашему клиенту
     * @param {TaxiMethod} method - стуктура для выполнения метода на наш клиент
     * @returns {undefined}
     */
    self.executeQuery = function(method) {
        if (!self.enabled){
            console.log('Not executed query, because TaxiClient is not enabled');
            return false;
        }
        if (typeof (method) !== 'object') {
            console.log('Запрос должен быть типа TaxiMethod');
            console.log(method);
            return false;
        }
        var url = self.ajaxClientUrl + '?command=' + method.name;
        $.ajax({
            async: method.async,
            url: url,
            type: 'post',
            timeout: method.timeout,
            data: method.params,
            dataType: 'json',
            /*
             * Проверка ответа на успешность и исполнение ответных функций
             * обработчиков
             */
            success: function(response) {
                if (parseInt(response.status) > 0) {
                    method.successCallback(response.result);
                } else {
                    method.errorCallback();
                }
            },
            error: function() {
                /*
                 * Повтор запроса, если это было задано в методе
                 */
                if (method.tryCount > 1) {
                    method.tryCount = method.tryCount - 1;
                    console.log('Повтор отправки запроса, т.к. он был провален в первый раз, осталось попыток: ' + method.tryCount);
                    setTimeout(function() {
                        self.executeQuery(method);
                    }, method.tryPause);
                }
                method.errorCallback();
            }
        });
    };

    return self;
};