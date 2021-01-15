$(document).ready(function () {

    $('body').on('click', '.orders_filter input[type="text"]', function () {
        $(this).next('img').trigger('click');
    })


    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"

                ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    var result;
    $.ajax({
        type: "POST",
        url: "/api_integration/index_client.php?command=canGetOrderListFromAPI",
        //data: "json",
        async: false,
        success: function (data) {
            data = JSON.parse(data);
            result = data.result;
        }
    });

    // Если работаем через api ПО
    if (result === true) {
        $('.my-orders').empty();
        var phone = getCookie('BITRIX_SM_LOGIN');
        phone = phone.replace(/\D/g, "");
        if (!phone.trim())
            return;
        $('.my-orders').html('<span id="loader"><img style=""  src="/cabinet/loading.gif" alt=""/>Идет поиск заказов</span>');

        var startDate = $('#dateFrom').val();
        if (startDate) {
            var arDate = startDate.split(' ');
            var arStrDate = arDate[0].split('.');
            var startTime = arStrDate[2]+arStrDate[1]+arStrDate[0] + arDate[1];
            startTime = startTime.replace(/\D/g, "");
        }
        var endDate = $('#dateTo').val();
        if (endDate) {
            var arDate = endDate.split(' ');
            var arStrDate = arDate[0].split('.');
            var endTime = arStrDate[2]+arStrDate[1]+arStrDate[0] + arDate[1];
            endTime = endTime.replace(/\D/g, "");
        }
        $.ajax({
            type: "POST",
            url: "/api_integration/index_client.php?command=findOrderList",
            data: {phone: phone, startTime: startTime, endTime: endTime},
            async: true,
            success: function (data) {
                var res = JSON.parse(data);
                if (res.result == null || res.result == false || (res.result.length == 0)) {
                    $('#loader').empty();
                    $('#loader').html("<span>Список заказов отсутстует<span>");
                } else {
                    $.ajax({
                        type: "POST",
                        url: "/cabinet/ajax_show_orders_from_api.php",
                        data: {result: data, phone: phone},
                        async: true,
                        success: function (data) {
                            $('#loader').remove();
                            $('.my-orders').html(data);

                        },
                        error: function (error) {
                            $('#loader').empty();
                            $('#loader').html("<span>Список заказов отсутстует<span>");
                        }
                    });
                }
            },
            error: function (error) {
                $('#loader').empty();
                $('#loader').html("<span>Список заказов отсутстует<span>");
            }
        });
        $('body').on('click', 'div.start', function () {
            if ($(this).hasClass("block")) {
                return false;
            }
            var obj = $(this);
            var order_id = $(this).data('order_id');
            var order_block = $(this).parents('div.order-block');
            addBlock(obj);
            preloadShow(order_id);
            $.ajax({
                type: "POST",
                url: "/api_integration/index_client.php?command=getOrderInfo",
                data: {orderId: order_id},
                async: true,
                success: function (data) {
                    preloadHide(order_id);
                    console.log(data);
                    $.ajax({
                        type: "POST",
                        url: "/cabinet/ajax_update_order_status_api.php",
                        data: {result: data},
                        async: true,
                        success: function (html) {
                            preloadHide(order_id);
                            deleteBlock(obj);
                            order_block.html(html);
                        },
                        error: function (error) {
                            deleteBlock(obj);
                            preloadHide(order_id);
                        }
                    });
                }, error: function (error) {
                    deleteBlock(obj);
                    preloadHide(order_id);
                }
            });
        });
    }

    // Если работам через битрикс
    if (result === false) {
        $('body').on('click', 'div.order-block a.delete', function (e) {
            e.preventDefault();
            var order_id = $(this).data('id');
            var block = $(this).parents('div.order-block');

            $.post(
                    '/include/ajax_hidden_order.php',
                    { order_id: order_id },
            function (json) {
                if (json) {

                    block.remove();
                }
                else {

                    console.log(json);
                }
            },
                    'json'
                    );
        });
        $('body').on('click', 'div.start', function () {
            if ($(this).hasClass("block")) {
                return false;
            }
            var obj = $(this);
            var order_id = $(this).data('order_id');
            var element_id = $(this).data('element_id');
            var status = $(this).data('status');
            var order_block = $(this).parents('div.order-block');
            addBlock(obj);
            preloadShow(order_id);
            $.ajax({
                type: "POST",
                url: "/api_integration/index_client.php?command=getOrderInfo",
                data: "orderId=" + order_id,
                async: true,
                success: function (data) {
                    preloadHide(order_id);
                    data = JSON.parse(data);
                    console.log(data);
                    if (data.result && status != data.result.status) {
                        $.ajax({
                            type: "POST",
                            url: "/include/ajax_update_order_status_bitrix.php",
                            data: "ELEMENT_ID=" + element_id,
                            async: true,
                            success: function (html) {
                                preloadHide(order_id);
                                deleteBlock(obj);
                                order_block.html(html);
                            },
                            error: function (error) {
                                preloadHide(order_id);
                                deleteBlock(obj);
                            }
                        });
                    }
                    deleteBlock(obj);
                },
                error: function (error) {
                    preloadHide(order_id);
                    deleteBlock(obj);
                }
            });

        });
    }

    // Общая логика для при работе через апи и через битрикс
    function preloadShow(id) {
        // $('#' + id).find
        $('.start[data-order_id=' + id + ']').find(".back.refresh").css('display', 'none');
        $('.start[data-order_id=' + id + ']').find(".loading").css('display', 'inline-block');
    }

    function preloadHide(id) {
        $('.start[data-order_id=' + id + ']').find(".back.refresh").css('display', 'inline-block');
        $('.start[data-order_id=' + id + ']').find(".loading").css('display', 'none');
    }

    function addBlock(obj) {
        obj.addClass("block");
        obj.css('background-color', 'rgb(229, 235, 219)');
        obj.css('color', 'rgb(207, 201, 201)');

    }

    function deleteBlock(obj) {
        obj.removeClass("block");
        obj.css('background-color', 'white');
        obj.css('color', 'black');
    }

    $('body').on('click', 'a.repeat', function (e) {
        e.preventDefault();
        $(this).next('form[name="repeat_order"]').submit();
    });
    $('body').on('click', 'a.cancel', function (e) {
        var obj = $(this);
        e.preventDefault();
        var orderId = $(this).attr('data-id');
        preloadShow(orderId);
        addBlock(obj);
        $.ajax({
            type: "POST",
            url: "/api_integration/index_client.php?command=rejectOrder",
            data: "orderId=" + orderId,
            async: true,
            success: function (data) {
                preloadHide(orderId);
                deleteBlock(obj);
                data = JSON.parse(data);
                result = data.result;
                if (result == 1) {
                    $('.start[data-order_id=' + orderId + ']').click();
                    console.log('Заказ №' + orderId + ' успешно отменен');

                } else {
                    console.log('Не удалось отменить заказ');
                }

            },
            error: function (error) {
                preloadHide(orderId);
                deleteBlock(obj)
            }
        });
    });
});
