$(function() {
    $('body').on('reset', 'form.ajax_form', function(ev) {
        $(ev.currentTarget).find('.form_stars li').each(function () {
            $(this).find('span').removeClass('act2');
        })
    });
})
