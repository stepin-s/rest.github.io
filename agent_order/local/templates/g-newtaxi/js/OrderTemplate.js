taxiOrder.ready(function (TO) {
    /** Form Events **/
    document.getElementById(TO.config.html.form.inputs.id.from.address.street).addEventListener('keyup', keyupAddress);
    document.getElementById(TO.config.html.form.inputs.id.to.address.street).addEventListener('keyup', keyupAddress);

    document.getElementById(TO.config.html.form.inputs.id.from.autocomplete.street).addEventListener('click', autocompleteSelect);
    document.getElementById(TO.config.html.form.inputs.id.to.autocomplete.street).addEventListener('click', autocompleteSelect);

    document.getElementById(TO.config.html.form.inputs.id.tariffs).addEventListener('change', calcCost);
    $('.ds2_submits button').click(setCoords);

    document.getElementById(TO.config.html.form.buttons.id.createorder).addEventListener('click', validateParams);

    $('.change_phone').click(function () {
        TO.hasFormParams = false;
    });

    $('.reject_order').click(function (event) {
        var lang = TO.config.phrases.currentLang;
        if (confirm(TO.config.phrases[lang].rejectConfirm)) {
            TO.rejectOrder({orderId: taxiOrder.order.id}, function (result) {
                console.log(result)
            }, function (error) {
            });
        }
        event.preventDefault();
    });

    /** Form Events **/


    TO.initMap(function (map) {

        setInterval(function () {
            TO.findCars(function(cars){displayCars(cars)});
        }, TO.config.request.findCarsInterval);

    });


    TO.findTariffs(function (tariffs) {
        var $tariffs = document.getElementById(TO.config.html.form.inputs.id.tariffs);
        $($tariffs).empty();

        tariffs.forEach(function (el) {
            $($tariffs).append(createTariffTemplate(el));
        });
        var out = '<div class="ui_menu uim_select pr_sel"><a class="uim_title price_select" style="min-width: 130px"><span>';
        var i = 0;
        $('#' + TO.config.html.form.inputs.id.tariffs + ' option').each(function () {
            if (i == 0) {
                out = out + $(this).text() + '</span></a><ul>';
            }

            out = out + '<li><input type="radio" ' + (i == 0 ? 'checked' : '') + ' name="r02" id="cch' + i + '"><label title="' + $(this).text() + '" data-id="' + $(this).attr('data-id') + '" for="cch' + i + '"><span>' + $(this).text() + '</span></label></li>';
            i++;
        });
        out += '</ul></div>';
        $($tariffs).before(out);

        $('body').on('click', '.pr_sel label', function () {
            $($tariffs).find('option:selected').prop('selected', false);
            $($tariffs).find('option[data-id="' + $(this).attr('data-id') + '"]').prop('selected', true);
            $tariffs.dispatchEvent(new Event('change'));
        })

    }, function (error) {
    });

});

function keyupAddress(event) {
    if (taxiOrder.isRequestBusy())
        return;
    var direction = $(event.target).closest('.direction_input').attr('data-direction');
    var autocompleteWrapper = document.getElementById(taxiOrder.config.html.form.inputs.id[direction].autocomplete.street);
    var autocompleteInput = document.getElementById(taxiOrder.config.html.form.inputs.id[direction].address.street);
    if (event.target.value.length < 3) {
        $(autocompleteWrapper).removeClass('loading_autocomplete');
        return;
    }

    $(autocompleteInput).addClass('loading_autocomplete');
    taxiOrder.requestDisable(0);
    taxiOrder.findGeoObjects(
        {part: event.target.value, target: autocompleteInput},
        function (geoObjects) {
            $(autocompleteInput).removeClass('loading_autocomplete');
            var direction = $(event.target).closest('.direction_input').attr('data-direction');
            $(autocompleteWrapper).empty();
            if (geoObjects.length) {
                geoObjects.forEach(function (el) {
                    $(autocompleteWrapper).append(createGeoObjectTemplate(el));
                });
            }
            else {
                $(autocompleteWrapper).append(createGeoObjectTemplate(false));
            }

            $(autocompleteWrapper).addClass('active_autocomplete');
        }, function (error) {
        }
    );
    taxiOrder.requestEnable(taxiOrder.config.request.delay);
}

function autocompleteSelect(event) {
    var direction = $(event.target).closest('.direction_input').attr('data-direction');
    var autocompleteWrapper = document.getElementById(taxiOrder.config.html.form.inputs.id[direction].autocomplete.street);
    var autocompleteInput = document.getElementById(taxiOrder.config.html.form.inputs.id[direction].address.street);

    autocompleteInput.value = event.target.innerText;
    $(autocompleteWrapper).removeClass('active_autocomplete');

    setCoords(event);
    taxiOrder.drawRoute(direction, autocompleteInput.value);
}

function setCoords(event) {
    var direction = $(event.target).closest('.direction_input').attr('data-direction');
    var $street = document.getElementById(taxiOrder.config.html.form.inputs.id[direction].address.street);
    var $house = document.getElementById(taxiOrder.config.html.form.inputs.id[direction].address.house);
    var address = ($street.value.trim() + ' ' + $house.value.trim()).trim();

    ymaps.geocode(address, {results: 1}).then(function (res) {
            var coords = res.geoObjects.get(0).geometry.getCoordinates();
            $street.dataset.lat = '' + coords[0];
            $street.dataset.lon = '' + coords[1];
            calcCost();
        }
    );
}

function calcCost() {
    var fromStreet = document.getElementById(taxiOrder.config.html.form.inputs.id.from.address.street).value.trim();
    var toStreet = document.getElementById(taxiOrder.config.html.form.inputs.id.to.address.street).value.trim();
    if (!fromStreet || !toStreet)
        return;

    taxiOrder.formParams = takeFormParams();
    taxiOrder.hasFormParams = true;

    taxiOrder.calcCost(taxiOrder.formParams, function (result) {
        var lang = taxiOrder.config.phrases.currentLang;
        document.getElementById(taxiOrder.config.html.form.elements.id.result.calccost.label).innerText = taxiOrder.config.phrases[lang].calcCostTitle;
        document.getElementById(taxiOrder.config.html.form.elements.id.result.calccost.cost).innerText = result.cost + ' ' + taxiOrder.config.phrases[lang].calcCostCurrency;
        document.getElementById(taxiOrder.config.html.form.elements.id.result.calccost.time).innerText = result.time + ' ' + taxiOrder.config.phrases[lang].calcCostTime
        document.getElementById(taxiOrder.config.html.form.elements.id.result.calccost.dist).innerText = result.dist + ' ' + taxiOrder.config.phrases[lang].calcCostDist
        document.getElementById(taxiOrder.config.html.form.elements.id.result.calccost.sumcost).style.display = 'block';
    }, function (error) {
    });

    event.preventDefault();
}

function validateParams(event) {
    $('#' + taxiOrder.config.html.form.elements.id.result.info).empty();
    taxiOrder.formParams = takeFormParams();
    taxiOrder.hasFormParams = true;
    taxiOrder.validateParams(taxiOrder.formParams, function (result) {
        taxiOrder.needSendSms(taxiOrder.formParams.phone, function (need) {
            if (need) {
                taxiOrder.sendSms(taxiOrder.formParams.phone, function (result) {

                }, function (error) {
                });
                go_to_step(3);
            }
            else {
                taxiOrder.createOrder(taxiOrder.formParams, function (result) {
                    taxiOrder.getOrderInfoInterval({orderId: result}, function (result) {
                        displayOrderInfo(result);
                    }, function (error) {
                    });
                    var lang = taxiOrder.config.phrases.currentLang;
                    $('.order_status').html(taxiOrder.config.phrases[lang].orderCreated.replace('{id}', result));
                    go_to_step(4);
                }, function (error) {
                });
            }
        }, function (error) {
        });
    }, function (error) {
        $('#' + taxiOrder.config.html.form.elements.id.result.info).append(error.errors);
        taxiOrder.hasFormParams = false;
        console.log(error)
    });
    event.preventDefault();
}

function displayCars(cars) {
    var redCarIcon = L.icon({
        iconUrl: taxiOrder.config.map.mapRedCarIconSrc,
        iconSize: taxiOrder.config.map.mapRedCarIconSize,
        iconAnchor: taxiOrder.config.map.mapRedCarIconAnchor,
        popupAnchor: taxiOrder.config.map.mapRedCarPopupAnchor
    });

    var greenCarIcon = L.icon({
        iconUrl: taxiOrder.config.map.mapGreenCarIconSrc,
        iconSize: taxiOrder.config.map.mapGreenCarIconSize,
        iconAnchor: taxiOrder.config.map.mapGreenCarIconAnchor,
        popupAnchor: taxiOrder.config.map.mapGreenCarPopupAnchor
    });

    var carLayer = L.layerGroup();
    carLayer.clearLayers();

    var currentMarker, currentIcon;
    cars.forEach(function (item) {
        if (item.lat != null && item.lon != null) {
            currentIcon = (item.isFree == 0) ? redCarIcon : greenCarIcon;
            currentMarker = L.marker([item.lat, item.lon], {icon: currentIcon});
            currentMarker.bindPopup(item.description);
            carLayer.addLayer(currentMarker);
        }
    });
    taxiOrder.orderMap.addLayer(carLayer);
}

function displayOrderInfo(info) {
    $('.order_status').html(info.statusLabel);
    var lang = taxiOrder.config.phrases.currentLang;
    if ('car_assigned' == info.status) {
        $('.car_description').html(taxiOrder.config.phrases[lang].orderCar + ': ' + info.carDescription);
        $('.car_time').html(taxiOrder.config.phrases[lang].orderCarTime.replace('{min}', info.carTime));
        $('.driver_fio').html(taxiOrder.config.phrases[lang].orderDriver + ': ' + info.driverFio);
    }
    else if ('car_at_place' == info.status) {
        $('.car_time').remove();
        $('.reject_order').remove();
    }
    else if ('completed' == info.status || 'rejected' == info.status) {
        $('.reject_order').remove();
        $('.new_order').show();
        if (info.cost)
            $('.order_cost').html(taxiOrder.config.phrases[lang].orderCostTitle + ': ' + info.cost + ' ' + taxiOrder.config.phrases[lang].calcCostCurrency);
    }
}

function createTariffTemplate(el) {
    return '<option id="tariff-' + el.id + '" data-name="' + el.label + '" data-id="' + el.id + '" name="' + taxiOrder.config.html.form.inputs.name.tariffs + '">'
        + el.label
        + '</option>';
}

function createGeoObjectTemplate(el) {
    if (el)
        return "<li data-res='" + JSON.stringify(el) + "'>" + el.label + "</li>";

    var lang = taxiOrder.config.phrases.currentLang;
    return "<span>" + taxiOrder.config.phrases[lang].autocompleteNoResultsText + "</span>";
}

function takeFormParams() {
    var time = $('[name=' + taxiOrder.config.html.form.inputs.name.time + ']:checked').attr('value').trim()
        || ($('.time_selector select').val().trim() + ' ' + $('.time_selector [type=time]').val().trim()).trim()

    if (2 >= time.length)
        time = createDate(time);

    var formParams = {
        fromStreet: document.getElementById(taxiOrder.config.html.form.inputs.id.from.address.street).value.trim(),
        fromPorch: document.getElementById(taxiOrder.config.html.form.inputs.id.from.address.porch).value.trim(),
        fromHouse: document.getElementById(taxiOrder.config.html.form.inputs.id.from.address.house).value.trim(),
        comment: document.getElementById(taxiOrder.config.html.form.inputs.id.from.comment).value.trim(),
        toStreet: document.getElementById(taxiOrder.config.html.form.inputs.id.to.address.street).value.trim(),
        toHouse: document.getElementById(taxiOrder.config.html.form.inputs.id.to.address.house).value.trim(),
        priorTime: time.trim(),
        phone: document.getElementById(taxiOrder.config.html.form.inputs.id.phone).value.trim(),
        carType: $('#' + taxiOrder.config.html.form.inputs.id.tariffs).attr('value').trim(),
        tariffGroupId: $('#' + taxiOrder.config.html.form.inputs.id.tariffs).find('option:selected').attr('data-id').trim(),
        fromLat: document.getElementById(taxiOrder.config.html.form.inputs.id.from.address.street).dataset.lat,
        fromLon: document.getElementById(taxiOrder.config.html.form.inputs.id.from.address.street).dataset.lon,
        toLat: document.getElementById(taxiOrder.config.html.form.inputs.id.to.address.street).dataset.lat,
        toLon: document.getElementById(taxiOrder.config.html.form.inputs.id.to.address.street).dataset.lon,
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
        comment: ''
    }

    for (var key in formParams)
        if (allParams.hasOwnProperty(key))
            allParams[key] = formParams[key];

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