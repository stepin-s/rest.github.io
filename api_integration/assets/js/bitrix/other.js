function select_city(e) {
    $.colorbox.close();
    location.reload();
}


function preloader(images, callback) {
    var length = images.length;
    var count = 0;
    $.each(images, function(i, src) {
        var image = new Image();
        image.src = src;
        image.onload = function() {
            count++;
            if (count >= length) {
                callback();
            }
        };
    });
}
;
function get_max_height(elements) {
    var max = 0;
    $(elements).css({
        "height": "auto"
    });
    elements.each(function(i, element) {
        if (max < $(element).height()) {
            max = $(element).height();
        }
    });
    return max;
}
(function($) {
    $.fn.extend({
        transition: function(prop, val) {
            var prefix = ['-webkit-', '-moz-', '-o-', 'o-', ''];
            $(this).each(function() {
                var self = $(this);
                for (var i = prefix.length - 1, pref; i >= 0; i--) {
                    self.css(prefix[i] + 'transition' + prop, val);
                }
                ;
            });
            return $(this);
        }
    });
})(jQuery);
jQuery(document).ready(function($) {

    /**
     * Колорбокс - окно на ввод кода через СМС
     */
    $('.colorboxEnterCode').colorbox({
       //iframe: true,
        width: '90%',
        maxWidth: 562,
       // maxHeight: 385,
        autoScale: true,
        autoDimensions: true,
        onComplete: function() {
            $('#cboxClose').hide();
            $(window).off('resize.colorboxEnterCode');
            $(window).on('resize.colorboxEnterCode', function() {
                $.colorbox.resize({
                  //  width: ($(window).width() < 562) ? '90%' : 562,
                  //  height: $('.form_call_me').outerHeight()
                     width: ($(window).width() < 562) ? '90%' : 562
                });
            }).trigger('resize.colorboxEnterCode');
        }

    });
    /*
     * Маску ввода на поле с телефоном
     */
    $("#FIELD_TEL").mask("8 (999) 999-99-99");
    /**
     * Визуальные исправления для новой верстки
     */
    if ($('.detailed-order-taxi-wrapper').length) {
        $('.sprite.date').on('click', function(event) {
            $('img.calendar-icon').click();
            event.preventDefault();
        });
    }

    /**
     * Запрет для нажатия символьных клавиш в зоне контрола-инпута ввода даты
     */
    $('.control-group:has(label[for=FIELD_DATA]) input, .order-taxi-when-date input').on('keydown', function(event) {
        console.log(event.keyCode);
        if (event.keyCode > 48) {
            event.stopPropagation();
            event.preventDefault();
        }
    });
    /**
     * Выбор клиенат для такси-каталога
     */
    $('#select_client').on('change', function() {
        var service = $('#select_client option:selected').data('client');
        var id = $('#select_client option:selected').data('id');
        $.post('/include/ajax_name_service.php', {service_name: service, service_id: id}, function(data, textStatus) {
            if (textStatus === 'success') {
                location.reload();
            }
        });
    });
    /**
     * Вызов битрикс JS календаря для выбора даты и времени при клике внутри инпута ввода
     * даты
     */
    $('.control-group:has(label[for=FIELD_DATA]) input, .order-taxi-when-date input').on('click', function(event) {
        var _this = $(this);
        console.log(_this);
        if (_this.is('.order-taxi-when-date input')) {
            _this.closest('div').find('img.calendar-icon').click();
        } else {
            _this.closest('.controls').find('img.calendar-icon').click();
        }
    });
    /**
     * Обновление стоимости маршрута при изменени времени
     */
    $('.control-group:has(label[for=FIELD_DATA]) input, .order-taxi-when-date input').on('change', function() {
        /* ! TODO */
        taxi.updateRoute(true);
        taxi.updateRoute(false);
    });
    /* $('#map').css({
     'visibility': 'hidden',
     'height':1
     });*/

    $('#select_client').on('change', function() {
        var service = $('#select_client option:selected').data('client');
        var id = $('#select_client option:selected').data('id');
        $.post('/include/ajax_name_service.php', {service_name: service, service_id: id}, function(data, textStatus) {
            if (textStatus == 'success') {
                location.reload();
            }
        });
    });
    $('.pokazat a').on('click', function()
    {
        /*if ($(this).hasClass('active')) {
         $('#map').css({
         'visibility': 'visible',
         'height': 400,
         'margin-bottom':40
         });
         } else {
         $('#map').css({
         'visibility': 'hidden',
         'height':1,
         'margin-bottom':0
         });
         }*/
        $("#map").toggleClass('active');
    }
    );
    $('#myTab a').on('click', function() {
        if ($(this).attr('href') == '#quick')
            $('.no_quickly').css('display', 'none');
        else
            $('.no_quickly').css('display', 'block');
    });
    $('.form_stars li').on('click', function() {
        $('.form_stars .star').removeClass('active');
        $(this).addClass('active');
        $('#rating_value').val($(this).index() + 1);
    });
    /*
     * ?? что-то с оформлениями кнопок на сабмит - формах - портит новую верстку
     */
    if (!$('.detailed-order-taxi-wrapper').length) {
        var button = $('button[name=iblock_submit]');
        var submit = $('input[name=iblock_submit]');
        button.toggle();
        submit.toggle();
        $("#submit_anketa").toggle();
        button.on('click', function() {
            submit.click();
        });
    }


    $('.colorbox_form').colorbox({
        width: '90%',
        maxWidth: 562,
        height: 385,
        autoScale: true,
        autoDimensions: true,
        onComplete: function() {
            $('#cboxClose').hide();
            $(window).off('resize.colorbox_form');
            $(window).on('resize.colorbox_form', function() {
                console.log('sdf');
                $.colorbox.resize({
                  //  width: ($(window).width() < 562) ? '90%' : 562,
                    // Модальные окна (авторизация регистраиця перезвони мне)
                  //  height: $('.form_call_me').outerHeight()
                    width: ($(window).width() < 562) ? '90%' : 562
                });
            }).trigger('resize.colorbox_form');
        }
    });
    // var max = 0;  
    var images = [];
    var thumbs = $(".thumbnails .thumbnail");
    thumbs.find("img").each(function(indx, element) {
        images.push($(this).attr('src'));
    });
    preloader(images, function() {
        thumbs.transition('-duration', '0s').css({
            "height": get_max_height(thumbs)
        });
    });
    $(window).on('resize', function() {
        thumbs.css({
            "height": get_max_height(thumbs)
        });
    });
    var maks = 0;
    $(".in .serv").each(function(indx, element) {
        if (maks < $(element).height()) {
            maks = $(element).height();
        }
    });
    $(".in .serv").height(maks);
});