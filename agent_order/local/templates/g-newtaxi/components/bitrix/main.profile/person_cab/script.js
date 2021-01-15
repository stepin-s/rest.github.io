$(function() {
    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
                ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    $.ajax({
        type: "POST",
        url: "/api_integration/index_client.php?command=getClientBalanceInfo",
        data: "phone=" + getCookie('BITRIX_SM_LOGIN'),
        async: false,
        success: function(data) {
        	try{
	            data = JSON.parse(data);
	            $('#clientBalanceInfo').show();
	            $('#balance').html(data.result.balance);
	            $('#bonusBalance').html(data.result.bonusBalance);
        	} catch(e){}

        },
        error: function(error) {

        }
    });

    $(document).on('click','#save_profile',function() {
        var form = $('#profile_form');
        var login = form.find("input[name='LOGIN']").val();
        login = login.replace(/\D/g, "");
        var form_data = form.serializeArray();
        form_data[5].value = form_data[5].value.replace(/\D/g, "");
        form_data[6].value = form_data[6].value.replace(/\D/g, "");
        $.post(
                '/cabinet/check_phone_cab.php',
                {login: login},
        function(json) {
            var need_keys = form.find("input[name='save']").data('index');
            if (json.status == 1 || need_keys == 1)
            {
                $.post(
                        '/api_integration/index_client.php?command=needSendSms',
                        {phone: login},
                function(json) {
                    if (json.result == 1)
                    {
                        $.post(
                                '/api_integration/index_client.php?command=sendSms',
                                {phone: login},
                        function(json) {
                            if (json.result.success)
                            {
                                $.colorbox({
                                    href: '/cabinet/enter_code_cab.html',
                                    width: '90%',
                                    maxWidth: 562,
                                    maxHeight: 485,
                                    autoScale: true,
                                    autoDimensions: true,
                                    onComplete: function() {
                                        $('#cboxClose').hide();
                                    }
                                });
                            }
                        },
                                'json'
                                );
                    } else {
                        form.submit();
                    }
                },
                        'json'
                        );
            }
            else
                form.submit();
        },
                'json'
                );
    });

    $('body').on('click', '#confirm_code', function() {
        var sms_code = $("#smsCode");

        if (sms_code.val() == '')
            sms_code.addClass('error');
        else
        {
            var form = $('#profile_form');
            var phone = form.find("input[name='LOGIN']").val();
            phone = phone.replace(/\D/g, "");
            sms_code.removeClass('error');

            $.post(
                    '/api_integration/index_client.php?command=login',
                    {phone: phone, smsCode: sms_code.val()},
            function(json) {
                if (json.result.success)
                {
                    form.append('<input type="hidden" name="UF_BROWSER_KEY" value="' + json.result.browserKey + '">');
                    form.append('<input type="hidden" name="UF_TOKEN" value="' + json.result.token + '">');
                    form.submit();
                }
                else
                    sms_code.addClass('error');
            },
                    'json'
                    );
        }
        return false;
    });

    $('#profile_form input[name="LOGIN"]').blur(function() {
        var new_login = $(this).val();
        var old_login = $('#profile_form input[name="LOGIN_OLD"]').val();
        var need_keys = $("#profile_form input[name='save']").data('index');

        if (new_login != old_login || need_keys == 1)
        {
            $.post(
                    '/api_integration/index_client.php?command=needSendSms',
                    {phone: new_login},
            function(json) {
                if (json.result == 1)
                    $('#profile_form div.warning').show();
            },
                    'json'
                    );
        }
        else
            $('#profile_form div.warning').hide();

    });
});