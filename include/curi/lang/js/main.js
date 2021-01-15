$(function () {

    $('.choose_lang').change(function () {

        var old_element = $(this).data('element');
        var new_element = $(this).find(':checked').data('element');

        if (old_element === new_element)
            return false;

        var object = $(this).attr('id');

        var data = {'lang-ajax': true, 'object': object, 'new': new_element, 'old': old_element, 'path': location.pathname + location.search};

        $.post('', data, function (response) {
            if (response.path) {
                if (response.js_cookie) {
                    var nd = (new Date(response.js_cookie.expire)).toUTCString();
                    document.cookie = response.js_cookie.value+";path="+response.js_cookie.path+";expires="+nd+";";
                }
                location.href = response.path;
            }
        }, 'json');
    });



})