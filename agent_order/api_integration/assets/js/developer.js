/* 
 * Общего назначения
 */
$(document).ready(function() {
    $('.showLink').on('click', function(event) {
        event.stopPropagation();
        event.preventDefault();
        $(this).closest('td').find('div').toggleClass('hidden');
    });
    $('.ajaxRun').on('click', function(event) {
        event.stopPropagation();
        event.preventDefault();
        var _this = $(this);

        var url = _this.attr('href');
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            data: {},
            beforeSend: function() {
                var d = new Date();
                _this.startTime = d.getTime();
                _this.closest('tr').find('.resultTime').html('processing ...');
            },
            success: function(response) {
                d = new Date();
                var delta = d.getTime() - _this.startTime;
                _this.closest('tr').find('.resultTime').html(delta + ' ms');
                _this.closest('tr').find('.resultRun').html(response.htmlResult);
                _this.closest('tr').find('.resultJson').html(response.jsonResult);
            }
        });
    });
});


