/**
 * namespace
 * @type type
 */
var smsCodeWindow = {};
/* 
 * JS окна ввода СМС кода
 */
jQuery(document).ready(function($) {
    /**
     * Закрыть окно ввода кода
     * @returns {undefined}
     */
    smsCodeWindow.close = function() {
        $.colorbox.close();
        parent.taxi.ordering.unlock();
    };

    /**
     * Отображение статусного сообщения
     * @param {string} message
     * @returns {undefined}
     */
    smsCodeWindow.setStatusMessage = function(message) {
        var html = '';
        if (message !== '') {
            html = '<p>' + message + '</p>';
        }
        if ($('#statusLine').length > 0) {
            $('#statusLine').html(html);
        } else {
            $('#errorResult').after('<div id="statusLine">' + html + '</div>');
        }
        $.colorbox.resize({
           // height: 465
        });
    };

    /**
     * Отображение сообщения об ошибках
     * @param {html} message
     * @returns {undefined}
     */
    smsCodeWindow.setErrorMessage = function(message) {
        $('#errorResult').html(message);
        //   parent.$.colorbox.resize({
        $.colorbox.resize({
           // height: 465
        });
    };

    $('button[name=iblock_submit]').on('click', function(event) {
        smsCodeWindow.setStatusMessage('отправка ...');
        $.colorbox.resize();

        event.stopPropagation();
        event.preventDefault();

        var code = $('#smsCode').val();
        var phone = parent.$('#FIELD_TEL').val();
        var process = parent.window.taxi.ordering;

        /*
         * Иначе
         * Проводим валидацию параметров: может быть из-за неверного
         * формата не удалось провести Запрос авторизации?
         */
        process.sendToClient('validateCommand', {
            command: 'login',
            paramsToValidate: {
                phone: phone,
                smsCode: code
            }
        }, function(validationResult) {
            if (validationResult.hasErrors) {
                /* Показываем ошибки */
                smsCodeWindow.setErrorMessage(validationResult.errorsInfo.summaryText);
            } else {
                /* Отправка кода авторизации на на наш клиент */
                process.sendToClient('login', {
                    phone: phone,
                    smsCode: code
                }, function(loginResult) {
                    console.log(loginResult);
                    if (loginResult.isAuthorizedNow && loginResult.success) {
                        /* Если все хорошо, авторизация успешна, то
                         * закрываем колорбокс
                         * отправляем форму
                         */
                       $.colorbox.close();
                        /*
                         * Разблокируем клиент,
                         * создаем заказ,
                         */
                        process.afterLogin(loginResult);
                        process.unlock();
                        process.createOrder(process._lastValidationResult);
                    } else {
                        smsCodeWindow.setStatusMessage('');
                        /* Введенный код может быть неверным */
                        if (loginResult.text) {
                            smsCodeWindow.setErrorMessage(loginResult.text);
                        } else {
                            smsCodeWindow.setErrorMessage('Ошибка авторизации: код может быть неверным?');
                            $.colorbox.resize();
                        }
                    }
                }, function() {
                    smsCodeWindow.setStatusMessage('');
                    alert('Внутренняя ошибка при отправке кода');
                    smsCodeWindow.setErrorMessage('Внутренняя ошибка при отправке кода');
                     $.colorbox.resize();
                });
            }
        });
    });

});