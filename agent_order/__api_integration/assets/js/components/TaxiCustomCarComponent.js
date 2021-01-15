/*
 * 
 * Компонент процесса заказа - работа с произвольным заказом машины (эпипажа / борта)
 */

/**
 * Настройки и спец.функии при создании заказ с произвольной машиной
 * @returns {TaxiCustomCarComponent.self}
 */
function TaxiCustomCarComponent()
{
    var self = {};
    /**
     * Флаг включения этого подкомпонента
     */
    self.enabled = false;
    /**
     * Таймаут на создание заказа с произвольной машиной, мс
     */
    self.createTimeout = 30000;
    /**
     * Сообщения
     */
    self.messages = {
        createCarDefault: 'Желаемый вами борт находится далеко, предлагаем сделать заявку на ближайший борт?'
    };
    /**
     * Родительский объект - это обчычно просто процесс заказа 
     * TaxiOrderProcess
     */
    self.owner;
    /**
     * Попытка создания заказа без произвольной машины
     * @returns {undefined}
     */
    self._tryCreateOrderAgain = function() {

    };
    /**
     * Проверить назначение машины в информации о заказе +
     * при необходимости вывод вопроса с информацией
     * @returns {undefined}
     */
    self.checkOrderCar = function() {
        var info = self.owner.lastOrderInfo;
        // если не никакой информации о машине в заказе
        console.log('Проверка информации о машине');
        console.log(info);
        if (info && !info.carColor && !info.carId && !info.carNumber && !info.carTime) {
            console.log('Провалено');
            // выводим вопросительное окно с нашим вопросом:
            self.owner.modalConfirm(self.messages.createCarDefault,
                    [{
                            label: 'Нет',
                            callback: function() {
                                // разблокируем клиент
                                self.owner.unlock();
                                // отменим заказ
                                self.owner.rejectOrder(function() {
                                    // продолжим стандартные действия после отмены заказа
                                    return true;
                                });
                                return false;
                            }
                        },
                        {
                            label: 'Да',
                            callback: function() {
                                // разблокируем клиент
                                self.owner.unlock();
                                // отменим заказ
                                self.owner.rejectOrder(function() {
                                    console.log(self.owner.lastCreateOrderValidationResult);
                                    if (self.owner.lastCreateOrderValidationResult) {
                                        console.log(self.owner._updatingOrder);
                                        self.owner.createOrder(self.owner.lastCreateOrderValidationResult);                                        
                                    }
                                    // запрет на стандартные действия после отмены заказа
                                    return false;
                                });
                                return true;
                            }
                        }]
                    );
        }
    };
    /**
     * Событие возникает при начале обновления информации по заказу
     * @param {TaxiOrderProcess} ordering - процесс заказа
     * @returns {undefined}
     */
    self.onOrderInfoUpdating = function(ordering) {
        if (!self.enabled) {
            return false;
        }
        self.owner = ordering;
        if (ordering.orderId && (ordering.customCarId > 0 )) {
            console.log('checking order cars');            
            self.enabled = false;
            setTimeout(function() {
                self.checkOrderCar();
            }, self.createTimeout);
        }
    };
    return self;
}