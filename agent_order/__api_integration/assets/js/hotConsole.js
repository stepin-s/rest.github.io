/* 
 * Общего назначения
 */
function HotConsole() {
    HotConsole = {
        outDiv: $('.hotConsole .output'),
        statusDiv: $('.hotConsole .statusLine'),
        url: '/api_integration/developer?action=actionAjaxUpdateHotConsole',
        timeout: 3000
    };
    HotConsole.setStatus = function(message) {
        HotConsole.statusDiv.text(message);
    };
    HotConsole.init = function() {
        HotConsole.setStatus('Загружаю ...');
    };
    HotConsole.update = function() {
        var url = HotConsole.url;
        HotConsole.setStatus('Загружаю ... ...');
        $.ajax({
            url: url,
            type: 'get',
            data: {
            },
            success: function(response) {
                if (response.trim() != '') {
                    HotConsole.outDiv.find('p').removeClass('new');
                    HotConsole.outDiv.html(HotConsole.outDiv.html() + '<p class="new">' + response + '</p>');
                    HotConsole.setStatus('Готово. Ожидание');

                    function scroll_to_bottom(speed) {
                        var height = $("body").height();
                        $("html,body").animate({"scrollTop": height}, speed);
                    }
                    scroll_to_bottom(20);
                }
            }
        });
        setTimeout(function() {
            HotConsole.update();
        }, HotConsole.timeout);
    };
    HotConsole.init();
    return HotConsole;
}
;


$(document).ready(function() {

    var hotConsole = new HotConsole();
    hotConsole.update();

});


