
var maskList = $.masksSort($.masksLoad("/jobs/driver-form/inputmask/phone-codes.json"), ['#'], /[0-9]|#/, "mask");
var maskOpts = {
    inputmask: {
        definitions: {
            '#': {
                validator: "[0-9]",
                cardinality: 1
            }
        },
        //clearIncomplete: true,
        showMaskOnHover: false,
        autoUnmask: true
    },
    match: /[0-9]/,
    replace: '#',
    list: maskList,
    listKey: "mask",
    onMaskChange: function (maskObj, determined) {
        if (determined) {
            var hint = maskObj.name_ru;
            if (maskObj.desc_ru && maskObj.desc_ru != "") {
                hint += " (" + maskObj.desc_ru + ")";
            }
            $(this).next('span').html(hint);
        }
        $(this).attr("placeholder", $(this).inputmask("getemptymask"));
    }
};

$('.mask_phone').inputmasks(maskOpts);

var $wrapper = $('.driver-form'),
    $headerStep = $('.js-df-header-step'),
    $formStep = $('.js-df-step'),
    $jump = $('.js-df-jump'),
    body = $('body'),
	lang = 'ru';

var jumpToStep = function(event) {
    var step = Number($(this).data('jump-step')),
		stepsActive = $('.driver-form__header__item.active'),
		current = $(stepsActive[stepsActive.length-1]).data('step');
	
	if (current < step && !checkErrors.call(event.target))
		return;
	
    $formStep.removeClass('active');

    $headerStep.each(function () {
        var currentStep = Number($(this).data('step'));
        if(currentStep<=step) $(this).addClass('active');
        else $(this).removeClass('active');
    });

    $('[data-step="'+step+'"]').addClass('active');

    var top = $('h1').offset().top;

    $('html, body').animate({scrollTop: top-20}, 200);
};

$jump.on('click', jumpToStep);


//File input

$('.file-input input[type="file"]').on('dragover', function() {
    $(this).parents('.file-input').addClass('hover');
}).on('dragleave', function() {
    $(this).parents('.file-input').removeClass('hover');
});

$(body).on('dragover dragenter drop', function (e) {
    if(!$('.file-input input[type="file"]').is(e.target) && $('.file-input input[type="file"]').has(e.target).length === 0){
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
});

$('.file-input input[type="file"]').on('change', function (e) {
    e.preventDefault();

    $(this).parents('.file-input').parent().find('.loading-result').html('');

    var files = $(this)[0].files, _this = $(this);

    if (files.length>0){
        if(files[0].type.indexOf('image') !== -1 && files[0].size <= 4097152){
            $(this).parents('.file-input').removeClass('hover').addClass('loading');
            var fd = new FormData();
            fd.append('file', files[0]);

            setTimeout(function () {
                $.ajax({
                    url: '/api/index.php?upload',
                    data: fd,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    timeout: 30000,
                    success: function (data) {
                        var fI = $(_this).parents('.file-input');
                        if (data.code === 'OK'){
                            $(_this).parents('.row').removeClass('error');
                            $(fI).removeClass('loading').addClass('active').find('.file-name').html(files[0].name+'<a class="del"></a>');
                            $(fI).find('input[type="hidden"]').val(data.response);
                        } else{
                            $(fI).find('input[type="hidden"]').val('');
                            $(fI).removeClass('loading').find('input[type="file"]').val('');
                            $(fI).parent().find('.loading-result').html('Ошибка загрузки файла');
                        }

                    },
                    error: function (data) {
                        var fI = $(_this).parents('.file-input');
                        $(fI).removeClass('loading')
                            .removeClass('hover')
                            .find('input[type="hidden"]').val('');
                        $(fI).find('input[type="file"]').val('');
                        $(fI).parent().find('.loading-result').html('Ошибка загрузки файла');
                    }
                });
            }, 500);
        } else{
            if(files[0].type.indexOf('image') === -1)
                $(this).parents('.file-input').parent().find('.loading-result').html('Допускаются только изображения');
            else if (files[0].size > 4097152)
                $(this).parents('.file-input').parent().find('.loading-result').html('Максимальный рамер файла 4MB');
            else $(this).parents('.file-input').parent().find('.loading-result').html('Неизвестная ошибка');
        }
    }
})

$(body).on('click', '.file-name .del', function () {
    $(this).parents('.file-input')
        .removeClass('active')
        .parent().find('input[type="file"]').val('')
        .parent().find('input[type="hidden"]').val('');
})

$('.submit-form').on('click', function (e) {
	e.preventDefault();

	if (!checkErrors())
		return;

	$(this).addClass('loading_button');

	var form = $('form.driver-form'),
		$error = form.find('.driver-form-error'),
		$success = form.find('.driver-form-success');

	$success.empty();
	$error.empty();
		
	var fd = new FormData($(form)[0]);

	$.ajax({
		url: '/api/index.php?createWorker',
		data: fd,
		contentType: false,
		processData: false,
		type: 'POST',
		success: function (data) {
			$('.driver-form-form').hide();
			if (data.code === 'ERROR') {
				var errors = [];
				for (var field in data.response) {
					if ($.isArray(field))
						field.forEach(function(item) {errors.push(item);});
					else
						errors.push(data.response[field]);
				}
				$error.html(errors.join("<br>"));
				$error.show();
			}
			else if (data.code === 'OK') {
				$(form)[0].reset();
				$error.hide();
				$success.html('Спасибо. Ваша анкета успешно отправлена.');
				$success.show();
			}
			$('.submit-form').removeClass('loading_button');
		},
		error: function () {
			$('.driver-form-form').hide();
			$error.show();
			$('.submit-form').removeClass('loading_button');
		}
	});
})

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function checkErrors() {
	if (this == null)
		return false;
	
	var errors = 0;
	var step = Number($(this).attr('data-jump-step'));

	$(this).parents('.driver-form__content__item').find('input:not([type="checkbox"]), select').each(function () {
		if (!$(this).val() || $(this).val().trim() == ''){
			$(this).parents('.row').addClass('error');
			errors++;
		}
		else {
			if ($(this).attr('type') != 'email')
				$(this).parents('.row').removeClass('error');
			else if(validateEmail($(this).val())) 
				$(this).parents('.row').removeClass('error');
			else {
				$(this).parents('.row').addClass('error');
				errors++;
			}
		};
	})

	return (errors === 0);
}










/*****************************************/








var doAjax = function(method) {
    var url = '/api/' + method.name;

    $.ajax({
        async: (typeof(method.async)!== 'undefined') ? method.async : true,
        url: url,
        type: method.type,
        timeout: (typeof(method.timeout)!== 'undefined') ? method.timeout : 30000,
        data: (typeof(method.params)!== 'undefined') ? method.params : false,
        dataType: 'json',
        success: function(response) {
            method.successCallback(response);
        },
        error: function() {
            method.errorCallback();
        }
    });
    return self;
};



var state = 0;

var getData = {
    name: 'index.php?getData',
    tryCount: 2,
    type: 'post',
    params: {
      lang: lang
    },
    successCallback: function (result) {

        if (state === 1){
            $('.main-loader').removeClass('active');
        } else{
            state = 1;
        }

        if (result.response && result.response != "") {
            var data = result.response;
            if (data.cities && data.cities!= []){
                var citiesHtml = '';
                for(var key in data.cities){
                    citiesHtml = citiesHtml + '<option value="' + key + '">' + data.cities[key] + '</option>';
                }

                $('#cities-list').html(citiesHtml);
                $('#cities-list').val(Cookies.get('CITY_GOOTAX_ID'));
            }

            if (data.carColors && data.carColors!= []){
                var colorsHtml = '';
                for(var key in data.carColors){
                    colorsHtml = colorsHtml + '<option value="' + key + '">' + data.carColors[key] + '</option>';
                }

                $('#colors-list').html(colorsHtml);
            }

            if (data.brands && data.brands!= []){
                var brandsHtml = '';
                for(var key in data.brands){
                    brandsHtml = brandsHtml + '<option value="' + key + '">' + data.brands[key] + '</option>';
                }


                $('#brands-list').html(brandsHtml);
				
				var getData = {
					name: 'index.php?getCarModel',
					tryCount: 2,
					type: 'post',
					params: {
						brandId: $('#brands-list option:first-child').attr('value')
					},
					successCallback: function (result) {
						if (result.response && result.response != "" && result.response != []) {
							var data = result.response;
								var brandsHtml = '';
								for(var key in data){
									brandsHtml = brandsHtml + '<option value="' + key + '">' + data[key] + '</option>';
								}


								$('#models-list').html(brandsHtml);
						}
					},
					errorCallback: function () {
						console.log('Ошибка получения данных');
					}
				};

				doAjax(getData);
				
				
            }

            if (data.position && data.position!= []){
                var positionHtml = '';
                for(var key in data.position){
                    positionHtml = positionHtml + '<option value="' + key + '">' + data.position[key] + '</option>';
                }


                $('#position-list').html(positionHtml);
            }
        }
    },
    errorCallback: function () {
        console.log('Ошибка получения данных');
    }
};

doAjax(getData);



$('body').on('change', '#brands-list', function () {

    $('#models-list').html('');
    //$('.model-select .jss_selected').text(locData['driver_form_choose']);
    $('.model-select input').val('');

    var getData = {
        name: 'index.php?getCarModel',
        tryCount: 2,
        type: 'post',
        params: {
            brandId: $(this).find(':selected').attr('value')
        },
        successCallback: function (result) {
            if (result.response && result.response != "" && result.response != []) {
				$('#models-list').removeAttr('disabled');
                var data = result.response;
                    var brandsHtml = '';
                    for(var key in data){
                        brandsHtml = brandsHtml + '<option value="' + key + '">' + data[key] + '</option>';
                    }


                    $('#models-list').html(brandsHtml);
            }
        },
        errorCallback: function () {
            console.log('Ошибка получения данных');
        }
    };

    doAjax(getData);


})





