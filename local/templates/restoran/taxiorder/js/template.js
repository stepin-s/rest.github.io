var d = {
    id: function (id) {
        return document.getElementById(id);
    },
    cl: function (data) {
        console.log(data)
    },
    cd: function (data) {
        console.dir(data)
    }
};

document.addEventListener('build', function (e) {
	runReady(e.detail);
}, false);

function runReady(taxiOrderProxy = false) {
	let obj = taxiOrder;
	if(taxiOrderProxy!=false)
		obj = taxiOrderProxy;
	obj.ready(function (TO) {

		taxiOrder.currentLang = 'ru';// d.id(TO.config.html.elements.id.orderForm).dataset.lang;

		/******** FORM EVENTS **************/

		//d.id(TO.config.html.elements.id.orderForm).addEventListener('calcCost', displayCost);


		d.id(TO.config.html.inputs.id.from.street).addEventListener('keyup', delay(inputAddress, taxiOrder.config.request.keyupDelay));
		d.id(TO.config.html.inputs.id.to.street).addEventListener('keyup', delay(inputAddress, taxiOrder.config.request.keyupDelay));

		d.id(TO.config.html.inputs.id.from.autocomplete).addEventListener('click', setRoutePoint);
		d.id(TO.config.html.inputs.id.to.autocomplete).addEventListener('click', setRoutePoint);

		//console.log(TO.config.html.inputs.id.from.house);
		//d.id(TO.config.html.inputs.id.from.house).addEventListener('change', setRoutePoint);
		//d.id(TO.config.html.inputs.id.to.house).addEventListener('change', setRoutePoint);

        //if(d.id(TO.config.html.inputs.id.tariffs) !== null) {
           // d.id(TO.config.html.inputs.id.tariffs).querySelector('select').addEventListener('change', displayCost);
           // d.id(TO.config.html.inputs.id.tariffs).querySelector('select').addEventListener('change', displayDopics);
        //}
       // console.log(d.id(TO.config.html.inputs.id.wishes));
		//if(d.id(TO.config.html.inputs.id.wishes) !== null) {
			//d.id(TO.config.html.inputs.id.wishes).querySelector('ul').addEventListener('change', calcCostEvent);
		//}
        //console.log(d.id(TO.config.html.buttons.id.smsCode));
		if(d.id(TO.config.html.buttons.id.smsCode) !== null) {
			d.id(TO.config.html.buttons.id.smsCode).addEventListener('click', smsConfirm);
		}

		if(d.id(TO.config.html.buttons.id.createOrder) !== null) {
			d.id(TO.config.html.buttons.id.createOrder).addEventListener('click', createOrder);
		}
		//d.id(TO.config.html.buttons.id.rejectOrder).addEventListener('click', rejectOrder);
		//d.id(TO.config.html.elements.id.findMe).addEventListener('click', findMe);

		$('body').on('click', '#promocode-check', checkPromo);

		/******** FORM EVENTS **************/

		TO.findTariffs(function (tariffs) {
		    //console.log(tariffs);
			//displayTariffs(tariffs);
			//taxiOrder.tariffs = tariffs;
			//displayDopics();
		}, function (error) {
		});


		TO.findCarsInterval(function (cars) {
			TO.drawCars(cars, function (result) {
			}, function (error) {
			});
		}, function (error) {
		});

	});

	/******* EVENT LISTENERS ***********/
}
if ($('.order-form2').length) {
	runReady();
	//console.log(TO);
}

function checkPromo(event) {
    let params = {
        phone: JSON.parse(localStorage.getItem('phone')),
        promocode: $('#promocode')[0].value.trim(),
    };

    if (params.promocode) {
        taxiOrder.validateParams(params, function(result) {
                console.log(result);
            })
    } else {
        console.log('11111');
    }

    
}

function rejectOrder(event) {
    if (!taxiOrder.orderParams.orderId) return;
    if (!confirm($('#element-order-form').attr('data-cancel-order'))) return;
    taxiOrder.rejectOrder(taxiOrder.orderParams.orderId, displayInfo);
    event.preventDefault();
}

function smsConfirm2(event) {
	console.log(taxiOrder);
    var phone = d.id(taxiOrder.config.html.inputs.id.userPhone).value.trim();
    var code = d.id(taxiOrder.config.html.inputs.id.smsCode).value.trim();


    $('.result-sms .error').text('');
    taxiOrder.login(phone, code, function (result) {
        var exp = new Date((new Date).getTime() + 3600 * 24 * 7 * 1000);
        document.cookie = 'api_browser_key=' + result.browserKey + ';path=/;expires=' + exp.toUTCString();
        document.cookie = 'api_token=' + result.token + ';path=/;expires=' + exp.toUTCString();
        createRealOrder();
    }, function (error) {
        $('.result-sms .error').text(error.text)
    })
}

function createRealOrder() {
    var formParams = takeFormParams();
    taxiOrder.createOrder(formParams, function (result) {
        if (parseInt(result) > 0) {
            displayInfo({id: result, status: 'new', statusLabel: ''});
            goToStep3();
            taxiOrder.orderParams.orderId = result;
            taxiOrder.getOrderInfo(taxiOrder.orderParams.orderId, function (result) {
                displayInfo(result);
            }, function (error) {
            });
            taxiOrder.timerGetOrderInfo = setInterval(function () {
                taxiOrder.getOrderInfo(taxiOrder.orderParams.orderId, function (result) {
                    displayInfo(result);
                }, function (error) {
                });
            }, taxiOrder.config.request.orderInfoInterval);
        }
    }, function (error) {
    });
}

function createOrder(event) {
    var formParams = takeFormParams();
    $('#result-message').html('')
    taxiOrder.validateParams(formParams, function(result) {
        if (result.check) {
            taxiOrder.needSendSms(formParams.phone, function (need) {
                if (need) {
                    taxiOrder.sendSms(formParams.phone, function (result) {
                        goToStep2();
                    }, function (error) {
                    });
                }
                else {
                    createRealOrder();
                }
            })
        }
    },function(error) {
        if (!error.check) {
            $('#result-message').html('<span class="err">'+error.errors+'</span>')
        }
    })
    event.preventDefault();
}

function findMe(event) {
    event.preventDefault();
}

function delay(callback, ms) {
  var timer = 0;
  return function() {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, ms || 0);
  };
}

function calcCostEvent(event) {
    if (event.target != event.currentTarget) return;

    displayCost(event);
    event.preventDefault();
}

function inputAddress(event) {
	$('.autocomplete_select').show();
    var direction = $(event.target).closest('.direction').data('direction');
    var $target = d.id(taxiOrder.config.html.inputs.id[direction].autocomplete);

    if (event.target.value.length < 3 || taxiOrder.isRequestBusy()) {
        $target.classList.contains('active_autocomplete') && $target.classList.remove('active_autocomplete');
        return;
    }

    var params = {
        part: event.target.value,
        //city: {lat: Cookies.get('CITY_LAT'), lon: Cookies.get('CITY_LON'), id: Cookies.get('CITY_GOOTAX_ID')}
	    city: {lat: "52.185587264597416", lon: "53.26461029", id: 26068}
    };
    taxiOrder.requestDisable();
    taxiOrder.findGeoObjects(params, function (objects) {
        taxiOrder.requestEnable(taxiOrder.config.request.keyupDelay);
        $target.innerHTML = '';
        objects.length ? displayObjects(objects, $target) : displayObjectsNotFound($target);
        $target.classList.add('active_autocomplete');
    }, function (error) {
    });
}

/******* EVENT LISTENERS ***********/



function displayTariffs(tariffs) {
    $tariffs = d.id(taxiOrder.config.html.inputs.id.tariffs);
    $select = $tariffs.querySelector('select');
    $ul = $tariffs.querySelector('ul');

    $tariffs.querySelector('.js-select-a').innerHTML = tariffs[0].label + '<span></span>';

    $select.innerHTML = '';
    tariffs.forEach(function (item) {
        $select.appendChild(createTariffTemplateSelect(item));
    });
    console.log(tariffs[0]);
    //tariffs[0].prop('selected', true);

    $ul.innerHTML = '';
    tariffs.forEach(function (item) {
        $ul.appendChild(createTariffTemplateUl(item));
    });


    $tariffs.style.display = 'block';


}

function displayDopics() {
    if (!taxiOrder.tariffs.length)
        return;

    var $dopics = $('#'+taxiOrder.config.html.inputs.id.wishes),
        $ulDopics = $dopics.find('ul'),
        arDopics = [],
        $tariffs = $('#'+taxiOrder.config.html.inputs.id.tariffs),
        $tariff = $tariffs.find(':selected') || $tariffs.find('option:first-child'),
        tariffId = $tariff.data('id');

        $ulDopics.html('');
        taxiOrder.tariffs.forEach(function(item) {
            if (tariffId == item.id) {
                arDopics = item.additional;
                item.additional.forEach(function(dopic) {
                    $ulDopics.append('<li><label><input type="checkbox" data-name="'+dopic.name+'" data-cost="'+dopic.price+'" data-id="'+dopic.id+'">'+dopic.name+'</label></li>');
                });
            }
        });
}

function createTariffTemplateSelect(el) {
    var option = document.createElement('option');
    option.id = 'tariff-' + (el.id || 0);
    option.dataset.name = el.label;
    option.dataset.value = (el.id || 0);
    option.dataset.id = (el.id || 0);

    return option;
}

function createTariffTemplateUl(el) {
    var li = document.createElement('li');
    var a = document.createElement('a');
    a.dataset.href = (el.id || 0);
    a.innerText = el.label;

    li.appendChild(a);

    return li;
}

function displayObjects(objects, $target) {
    objects.forEach(function (item) {
        var elem = document.createElement('li');
        elem.innerText = item.label;
        elem.dataset.res = JSON.stringify(item);

        $target.appendChild(elem);
    });
}

function displayObjectsNotFound($target) {
    var elem = document.createElement('span');
    elem.innerText = taxiOrder.config.phrases[taxiOrder.currentLang].autoCompleteNoResultsText;

    $target.appendChild(elem);
}

function setRoutePoint(event) {
    if (event.target.dataset.res === undefined) return; //click on not found geoobjects result

    var direction = $(event.target).closest('.direction').data('direction');

    var object = JSON.parse(event.target.dataset.res);
    var address = object.label;
    if (object.address.city) {
        //address += ' ' + object.address.city;
        d.id(taxiOrder.config.html.inputs.id[direction].street).dataset.city = object.address.city;
    }

    //taxiOrder.mapService.getCoords(address, function (coords) {
        var params = {coords: object.address.location, direction: direction};
        d.id(taxiOrder.config.html.inputs.id[direction].street).dataset.lat = params.coords[0];
        d.id(taxiOrder.config.html.inputs.id[direction].street).dataset.lon = params.coords[1];
        //console.log(object);

	var enterAdress = new CustomEvent("enterAdress_OLD", {
		"detail": object
	});

	document.dispatchEvent(enterAdress);


		$('.autocomplete_select').hide();
        /*taxiOrder.drawPoint(params, function (geoPoint) {
        }, function (error) {
        });*/
    //}, function (error) {
    //});
}

function displayCost(event) {
    if (!d.id(taxiOrder.config.html.inputs.id.from.street).value.trim() || !d.id(taxiOrder.config.html.inputs.id.to.street).value.trim())
        return;

    var langValue = Cookies.get('USER_LANG');
    
    taxiOrder.calcCost(takeFormParams(), function(result) {
        $('.result-cost').find('.dist').html(parseInt(result.dist,10) + ' км')
        $('.result-cost').find('.time').html(parseInt(result.time,10) + ' мин')
        $('.result-cost').find('.cost').html(parseInt(result.cost,10) + ' руб')
        $('.result-cost').show();
    }, function(error) {

    })
}

function takeFormParams() {
    var time = d.id(taxiOrder.config.html.inputs.id.orderTime).value.trim();

    if (2 >= time.length)
        time = createDate((time||10));

    var $wishes = $('#' + taxiOrder.config.html.inputs.id.wishes).find(':checked');
    var wishes = [];
    if ($wishes.length) {
        $.each($wishes, function (i, item) {
            wishes.push($(item).data('name'))
        });
    }
    wishes = wishes.join(',');

    var formParams = {
        fromCity: d.id(taxiOrder.config.html.inputs.id.from.street).dataset.city,
        fromStreet: d.id(taxiOrder.config.html.inputs.id.from.street).value.trim(),
        //fromPorch: d.id(taxiOrder.config.html.inputs.id.from.porch).value.trim(),
        //fromHouse: d.id(taxiOrder.config.html.inputs.id.from.house).value.trim(),
        comment: d.id(taxiOrder.config.html.inputs.id.from.comment).value.trim(),
        toCity: d.id(taxiOrder.config.html.inputs.id.to.street).dataset.city,
        toStreet: d.id(taxiOrder.config.html.inputs.id.to.street).value.trim(),
        //toHouse: d.id(taxiOrder.config.html.inputs.id.to.house).value.trim(),
        priorTime: time.trim(),
        phone: d.id(taxiOrder.config.html.inputs.id.userPhone).value.trim(),
        carType: $('#' + taxiOrder.config.html.inputs.id.tariffs).find('.js-select-a').text(),
        tariffGroupId: $('#' + taxiOrder.config.html.inputs.id.tariffs).find('option:selected').attr('data-id').trim(),
        fromLat: d.id(taxiOrder.config.html.inputs.id.from.street).dataset.lat,
        fromLon: d.id(taxiOrder.config.html.inputs.id.from.street).dataset.lon,
        toLat: d.id(taxiOrder.config.html.inputs.id.to.street).dataset.lat,
        toLon: d.id(taxiOrder.config.html.inputs.id.to.street).dataset.lon,
        additional: wishes.length ? wishes : '',
    };

    var allParams = {
        fromCity: '',
        fromStreet: '',
        fromHouse: '',
        fromHousing: '',
        fromBuilding: '',
        fromPorch: '',
        fromLat: '',
        fromLon: '',
        toCity: '',
        toStreet: '',
        toHouse: '',
        toHousing: '',
        toBuilding: '',
        toPorch: '',
        toLat: '',
        toLon: '',
        clientName: '',
        phone: '',
        priorTime: '',
        customCarId: '',
        customCar: '',
        carType: '',
        carGroupId: '',
        tariffGroupId: '',
        comment: '',
        isMobile: '',
        additional: ''
    }

    for (var key in formParams)
        if (allParams.hasOwnProperty(key))
            allParams[key] = formParams[key];

    //console.log(allParams.tariffGroupId);
    return allParams;
}

function addZero(i) {
    return (i < 10) ? '0' + i : i;
}

function createDate(time) {
    var d = new Date();
    d.setMinutes(d.getMinutes() + parseInt(time));
    return (addZero(d.getDate()) + '.' + addZero((d.getMonth() + 1)) + '.' + d.getFullYear() + ' ' + addZero(d.getHours()) + ':' + addZero(d.getMinutes()) + ':' + addZero(d.getSeconds()));
}


function displayInfo(info) {
    var lang = d.id(taxiOrder.config.html.elements.id.orderForm).dataset.lang;
    $('#order_status').text(info.statusLabel);
    switch (info.status) {
        case 'new':
            $('#order_id').text(info.id);
            if ('cost' in info && info.cost) {
                $('#order_price').closest('p').show();
                $('#order_price').text(info.cost + ' ' + taxiOrder.config.phrases[lang].calcCostCurrency);
            }
            $('#button-reject-order').show();
            break;
        case 'car_assigned':
            $('#order_car_mark span').text(info.carDescription);
            $('#order_car_time span').text(info.carTime + ' ' + taxiOrder.config.phrases[lang].calcCostTime);
            $('#order_driver span').text(info.driverFio);
            $('#order_car').show();
            break;
        case 'car_at_place':
            $('#button-reject-order').detach();
            $('#order_car_time').detach();
            break;
        case 'completed':
            clearInterval(taxiOrder.timerGetOrderInfo);
            break;
        case 'rejected':
            clearInterval(taxiOrder.timerGetOrderInfo);
            $('#button-reject-order').detach();
            break;
    }
}

function goToStep1() {
    $('.order_steps').removeClass('active');
    $('.step_1').addClass('active');
}

function goToStep2() {
    $('.order_steps').removeClass('active');
    $('.step_2').addClass('active');
}

function goToStep3() {
    $('.order_steps').removeClass('active');
    $('.step_3').addClass('active');
}