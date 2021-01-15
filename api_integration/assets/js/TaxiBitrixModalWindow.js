/*
 * 
 * Реализация модального окна в Битриксе в темах
 */

/**
 * Объект для управления стилизованными модальными окнами
 * @returns {unresolved}
 */
function TaxiBitrixModalWindow()
{
    var self = {};

    /**
     * Путь для вызова окна
     */
    self.ajaxColorBoxPath = '/api_integration/include/bitrix/modal.php';

    /**
     * Массив каллбеков
     */
    self.callbacks = [];

    /**
     * Обработчик после закрытия колорбокса
     * @returns {undefined}
     */
    self.onClose = function() {
        taxi.ordering.unlock();
        console.log('Colorbox - onClose');
    };

    /**
     * Вывод модального окна с необходимым сообщением и кнопками для подтверждения дейтсвий
     * @param {string} message - сообщение - может быть блоком html форматированного текста
     * @param {array} buttonsOptions - объект - массив с информацией о кнопках
     * @returns {}
     */
    self.customConfirm = function(message, buttonsOptions)
    {
        if (!$('#modalColorbox').length) {
            $('div').eq(0).after('<div id="modalColorbox" style="display: none"><a class="modalColorbox cboxElement" href="' +
                    self.ajaxColorBoxPath +
                    '">hiddenModal</a></div>'
                    );
            /**
             * Колорбокс - окно на ввод кода через СМС
             */
            $('.modalColorbox').colorbox({
                //iframe: true,
                width: '90%',
                maxWidth: 562,
               // maxHeight: 485,
                autoScale: true,
                autoDimensions: true,
                onComplete: function() {
                    $('#cboxClose').hide();

                    // Пропишем обработчик закрытия окна
                    $(window).off('cbox_closed.modalColorbox');
                    $(window).on('cbox_closed.modalColorbox', function() {
                        self.onClose();
                        $(window).off('cbox_closed.modalColorbox');
                    });

                    // Пропишем обработчик ресайза окна + выполним его ресайз
                    $(window).off('resize.modalColorbox');
                    $(window).on('resize.modalColorbox', function() {
                        $.colorbox.resize({
                           // width: ($(window).width() < 562) ? '90%' : 562,
                            //height: 445
                            // height: $('.form_call_me').outerHeight()
                             width: ($(window).width() < 562) ? '90%' : 562
                        });
                    }).trigger('resize.modalColorbox');
                }
            });
            $('#modalColorbox').data('modal_label', 'Внимание');
            $('#modalColorbox').data('modal_html', message);
        }

        $('#modalColorbox a').click();

        for (var i in buttonsOptions) {
            var n = i;
            n++;
            var nStr = n + '';
            $('#modalColorbox').data('modal_button_' + nStr, buttonsOptions[i].label);
            taxi.modal.callbacks[n] = buttonsOptions[i].callback;
        }
    };

    return self;
}
;


