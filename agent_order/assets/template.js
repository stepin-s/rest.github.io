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

if ($('#element-order-form').length) {
    taxiOrder.ready(function (TO) {

        taxiOrder.currentLang = d.id(TO.config.html.elements.id.orderForm).dataset.lang;

        /******** FORM EVENTS **************/

        d.id(TO.config.html.elements.id.orderForm).addEventListener('calcCost', displayCost);


        d.id(TO.config.html.inputs.id.from.street).addEventListener('input', inputAddress);
        d.id(TO.config.html.inputs.id.to.street).addEventListener('input', inputAddress);

        d.id(TO.config.html.inputs.id.from.autocomplete).addEventListener('click', setRoutePoint);
        //d.id(TO.config.html.inputs.id.to.autocomplete).addEventListener('click', setRoutePoint);

        //d.id(TO.config.html.inputs.id.from.house).addEventListener('change', setRoutePoint);
        //d.id(TO.config.html.inputs.id.to.house).addEventListener('change', setRoutePoint);

        $('body').on('change', '#' + TO.config.html.inputs.id.tariffs + ' input[type="radio"]', displayCost);
        $('body').on('change', '#input-tariffs input[type="radio"]', displayDopics);

        //d.id(TO.config.html.inputs.id.tariffs).querySelector('.options').querySelector('').addEventListener('change', displayCost);
        //d.id(TO.config.html.inputs.id.tariffs).querySelector('.options').querySelector('.options').addEventListener('change', displayDopics);
        d.id(TO.config.html.inputs.id.wishes).querySelector('ul').addEventListener('change', calcCostEvent);

        d.id(TO.config.html.buttons.id.smsCode).addEventListener('click', smsConfirm);

        d.id(TO.config.html.buttons.id.createOrder).addEventListener('click', createOrder);
        d.id(TO.config.html.buttons.id.rejectOrder).addEventListener('click', rejectOrder);
        d.id(TO.config.html.elements.id.findMe).addEventListener('click', findMe);

        /******** FORM EVENTS **************/

        TO.findTariffs(function (tariffs) {
            displayTariffs(tariffs);
            taxiOrder.tariffs = tariffs;
            displayDopics();
        }, function (error) {
        });


        /*TO.findCarsInterval(function (cars) {
            TO.drawCars(cars, function (result) {
            }, function (error) {
            });
        }, function (error) {
        });*/

    });

    /******* EVENT LISTENERS ***********/
}

function rejectOrder(event) {
    if (!taxiOrder.orderParams.orderId) return;

    var lang = d.id(taxiOrder.config.html.elements.id.orderForm).dataset.lang;

    if (!confirm(taxiOrder.config.phrases[lang].rejectConfirm)) return;
    taxiOrder.rejectOrder(taxiOrder.orderParams.orderId, displayInfo);
    event.preventDefault();
}

function smsConfirm(event) {
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

    collectInfo();

    var formParams = takeFormParams2();
    taxiOrder.createOrder(formParams, function (result) {
        if (parseInt(result) > 0) {
            var lang = d.id(taxiOrder.config.html.elements.id.orderForm).dataset.lang;

            displayInfo({id: result, status: 'new', statusLabel: taxiOrder.config.phrases[lang].new_order});
            goToStep(5);
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

//собираем информацию (агент, описание позиций) в комментарий для отправки в гутакс
function collectInfo() {
	
    let	$comment = $('#input-from-comment').val();//комментарий для водителя
    let	$agent = decodeURIComponent(getSearchParams('agent')); //код агента из урл
    
    let $descr = '';//инпут в форме доп опций с описанием

    if ($('#input-wishes ul li:last-child input').is( ":checked" ) ) {
    	$descr = $('#option-descr').val();
    }
    if ($agent === 'undefined') {
    	$('#input-from-comment').val('комментарий: ' + $comment + ', описание: ' + $descr);
    } else {
    	$('#input-from-comment').val('комментарий: ' + $comment + ', описание: ' + $descr + ', агент: ' + $agent);
    }
    console.log('комментарий: ' + $comment + ', описание: ' + $descr + ', агент: ' + $agent);
}

//достаем агента из урл
function getSearchParams(k){

	var p={};

	location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
    console.log(p[k], p);
	return k?p[k]:p;
}

//в точку Б подставляем точку А для рассчета
function takeAddress() {
    let lat = $('#input-from-street').data('lat');
    let lon = $('#input-from-street').data('lon');
    let val = $('#input-from-street').val();

    $('#input-to-street').data('lat', lat);
    $('#input-to-street').data('lon', lon);
    $('#input-to-street').val(val);
}

function createOrder(event) {
    var formParams = takeFormParams2();
    $('#result-message').html('')

    taxiOrder.validateParams(formParams, function (result) {
        if (result.check) {
            taxiOrder.needSendSms(formParams.phone, function (need) {
                if (need) {
                    taxiOrder.sendSms(formParams.phone, function (result) {
                        goToStep(4);
                    }, function (error) {
                    });
                }
                else {
                    createRealOrder();
                }
            })
        }
    }, function (error) {
        if (!error.check) {
            $('#result-message').html('<span class="err">' + error.errors + '</span>')
        }
    })
    event.preventDefault();
}

function findMe(event) {
    event.preventDefault();
}

function calcCostEvent(event) {
    if (event.target != event.currentTarget) {return;}
    //collectInfo();
    //takeFormParams2();
    displayCost(event);

    event.preventDefault();
}

function inputAddress(event) {
    var direction = $(event.target).closest('.direction').data('direction');
    var $target = d.id(taxiOrder.config.html.inputs.id[direction].autocomplete);

    if (event.target.value.length < 3 || taxiOrder.isRequestBusy()) {
        $target.classList.contains('active_autocomplete') && $target.classList.remove('active_autocomplete');
        return;
    }

    var params = {
        part: event.target.value,
        city: {lat: Cookies.get('CITY_LAT'), lon: Cookies.get('CITY_LON'), id: Cookies.get('CITY_GOOTAX_ID')}
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
    //$select = $tariffs.querySelector('select');
    $ul = $tariffs.querySelector('ul');

    //$tariffs.querySelector('.js-select-a').innerHTML = tariffs[0].label + '<span></span>';

    $ul.innerHTML = '';
    tariffs.forEach(function (item) {
        $($ul).append(createTariffTemplateSelect(item));
    });

    $($ul).find('input[type="radio"]').first().attr('checked', true);

    // $ul.innerHTML = '';
    // tariffs.forEach(function (item) {
    //     $ul.appendChild(createTariffTemplateUl(item));
    // });

    $tariffs.style.display = 'block';
}

function displayDopics() {
    if (!taxiOrder.tariffs.length)
        return;

    var $dopics = $('#' + taxiOrder.config.html.inputs.id.wishes),
        $ulDopics = $dopics.find('ul'),
        arDopics = [],
        tariffId = $('input[name="' + taxiOrder.config.html.inputs.id.tariffs + '"]:checked').val();
    console.log(arDopics, tariffId);

    $ulDopics.html('');
    $('#options').html('');
    taxiOrder.tariffs.forEach(function (item) {
        if (tariffId == item.id) {
            arDopics = item.additional;
            item.additional.forEach(function (dopic) {
                $ulDopics.append('<li><label><input type="checkbox" data-name="' + dopic.name + '" data-cost="' + dopic.price + '" data-id="' + dopic.id + '"><span>' + dopic.name + '</span></label></li>');
            });
        }
    });
}

function createTariffTemplateSelect(el) {
    // var option = document.createElement('option');
    // option.id = 'tariff-' + (el.id || 0);
    // option.dataset.name = el.label;
    // option.dataset.value = (el.id || 0);
    // option.dataset.id = (el.id || 0);

    var input = '<label><input type="radio" name="input-tariffs" value="' + (el.id || 0) + '"><span>' + el.label + '</span></label>';

    return input;
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
    var lang = d.id(taxiOrder.config.html.elements.id.orderForm).dataset.lang;
    elem.innerText = taxiOrder.config.phrases[lang].autoCompleteNoResultsText;

    $target.appendChild(elem);
}

function setRoutePoint(event) {
    if (event.target.dataset.res === undefined) return; //click on not found geoobjects result

    var direction = $(event.target).closest('.direction').data('direction');

    var object = JSON.parse(event.target.dataset.res);
    var address = object.label;
    if (object.address.city) {
        address += ' ' + object.address.city;
        d.id(taxiOrder.config.html.inputs.id[direction].street).dataset.city = object.address.city;
    }

    taxiOrder.mapService.getCoords(address, function (coords) {
        var params = {coords: coords, direction: direction};
        d.id(taxiOrder.config.html.inputs.id[direction].street).dataset.lat = coords[0];
        d.id(taxiOrder.config.html.inputs.id[direction].street).dataset.lon = coords[1];
        taxiOrder.drawPoint(params, function (geoPoint) {
        }, function (error) {
        });
    }, function (error) {
    });
}

function displayCost(event) {
    if (!d.id(taxiOrder.config.html.inputs.id.from.street).value.trim() || !d.id(taxiOrder.config.html.inputs.id.to.street).value.trim())
        {
            //console.log(d.id(taxiOrder.config.html.inputs.id.from.street).value, d.id(taxiOrder.config.html.inputs.id.to.street).value);
            return;}

    var lang = d.id(taxiOrder.config.html.elements.id.orderForm).dataset.lang;

    console.log(takeFormParams());
    taxiOrder.calcCost(takeFormParams(), function (result) {
        //$('.result-cost').find('.dist').html(result.dist + ' ' + taxiOrder.config.phrases[lang].calcCostDist);
        //$('.result-cost').find('.time').html(result.time + ' ' + taxiOrder.config.phrases[lang].calcCostTime);
        $('.result-cost').find('.cost').html(result.cost + ' ' + taxiOrder.config.phrases[lang].calcCostCurrency);
        $('.result-cost').show();
    }, function (error) {
        $('.result-cost').hide();
    })
}

function takeFormParams() {
    var time = d.id(taxiOrder.config.html.inputs.id.orderTime).value.trim();

    if (2 >= time.length)
        time = createDate((time || 10));

    var $wishes = $('#' + taxiOrder.config.html.inputs.id.wishes).find(':checked');
    var wishes = [];
    if ($wishes.length) {
        $.each($wishes, function (i, item) {
            wishes.push($(item).data('id'))
        });
    }
    wishes = wishes.join(',');

    var formParams = {
        fromCity: d.id(taxiOrder.config.html.inputs.id.from.street).dataset.city,
        fromStreet: d.id(taxiOrder.config.html.inputs.id.from.street).value.trim(),
        fromPorch: d.id(taxiOrder.config.html.inputs.id.from.porch).value.trim(),
        //fromHouse: d.id(taxiOrder.config.html.inputs.id.from.house).value.trim(),
        comment: d.id(taxiOrder.config.html.inputs.id.from.comment).value.trim(),
        toCity: d.id(taxiOrder.config.html.inputs.id.to.street).dataset.city,
        toStreet: d.id(taxiOrder.config.html.inputs.id.to.street).value.trim(),
        //toHouse: d.id(taxiOrder.config.html.inputs.id.to.house).value.trim(),
        priorTime: time.trim(),
        phone: d.id(taxiOrder.config.html.inputs.id.userPhone).value.trim(),
        carType: $('#' + taxiOrder.config.html.inputs.id.tariffs).find('.js-select-a').text(),
        tariffGroupId: $('input[name="' + taxiOrder.config.html.inputs.id.tariffs + '"]:checked').val().trim(),
        fromLat: d.id(taxiOrder.config.html.inputs.id.from.street).dataset.lat,
        fromLon: d.id(taxiOrder.config.html.inputs.id.from.street).dataset.lon,
        //toLat: d.id(taxiOrder.config.html.inputs.id.to.street).dataset.lat,
        //toLon: d.id(taxiOrder.config.html.inputs.id.to.street).dataset.lon,
        toLat: d.id(taxiOrder.config.html.inputs.id.from.street).dataset.lat,
        toLon: d.id(taxiOrder.config.html.inputs.id.from.street).dataset.lon,
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

    return allParams;
}

function takeFormParams2() {
    let time = d.id(taxiOrder.config.html.inputs.id.orderTime).value.trim();

    if (2 >= time.length)
        time = createDate((time || 10));

    let $wishes = $('#' + taxiOrder.config.html.inputs.id.wishes).find(':checked');
    let wishes = [];
    if ($wishes.length) {
        $.each($wishes, function (i, item) {
            wishes.push($(item).data('id'))
        });
    }
    wishes = wishes.join(',');

    let formParams = {
        fromCity: d.id(taxiOrder.config.html.inputs.id.from.street).dataset.city,
        fromStreet: d.id(taxiOrder.config.html.inputs.id.from.street).value.trim(),
        fromPorch: d.id(taxiOrder.config.html.inputs.id.from.porch).value.trim(),
        //fromHouse: d.id(taxiOrder.config.html.inputs.id.from.house).value.trim(),
        comment: d.id(taxiOrder.config.html.inputs.id.from.comment).value.trim(),
        //toCity: d.id(taxiOrder.config.html.inputs.id.to.street).dataset.city,
        //toStreet: d.id(taxiOrder.config.html.inputs.id.to.street).value.trim(),
        //toHouse: d.id(taxiOrder.config.html.inputs.id.to.house).value.trim(),
        priorTime: time.trim(),
        phone: d.id(taxiOrder.config.html.inputs.id.userPhone).value.trim(),
        carType: $('#' + taxiOrder.config.html.inputs.id.tariffs).find('.js-select-a').text(),
        tariffGroupId: $('input[name="' + taxiOrder.config.html.inputs.id.tariffs + '"]:checked').val().trim(),
        fromLat: d.id(taxiOrder.config.html.inputs.id.from.street).dataset.lat,
        fromLon: d.id(taxiOrder.config.html.inputs.id.from.street).dataset.lon,
        //toLat: d.id(taxiOrder.config.html.inputs.id.to.street).dataset.lat,
        //toLon: d.id(taxiOrder.config.html.inputs.id.to.street).dataset.lon,
        additional: wishes.length ? wishes : '',
    };

    let allParams = {
        fromCity: '',
        fromStreet: '',
        fromHouse: '',
        fromHousing: '',
        fromBuilding: '',
        fromPorch: '',
        fromLat: '',
        fromLon: '',
        //toCity: '',
        //toStreet: '',
        //toHouse: '',
        //toHousing: '',
        //toBuilding: '',
        //toPorch: '',
        //toLat: '',
        //toLon: '',
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

    for (let key in formParams)
        if (allParams.hasOwnProperty(key))
            allParams[key] = formParams[key];
    console.log(allParams);
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
