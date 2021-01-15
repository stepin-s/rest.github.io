taxiOrder.ready(function () {
  console.log('ready')
  console.log(taxiOrder)

  /** Form Events **/
  document.getElementById(taxiOrder.config.html.form.inputs.id.from.address.street).addEventListener('keyup', keyupAddress);
  document.getElementById(taxiOrder.config.html.form.inputs.id.to.address.street).addEventListener('keyup', keyupAddress);

  document.getElementById(taxiOrder.config.html.form.elements.id.getcost).addEventListener('click', calcCost);
  document.getElementById(taxiOrder.config.html.form.inputs.id.tariffs).addEventListener('change', calcCost);

  document.getElementById(taxiOrder.config.html.form.buttons.id.createorder).addEventListener('click', createOrder);
  /** Form Events **/

  taxiOrder.findTariffs(function (tariffs) {
    $tariffs = document.getElementById(taxiOrder.config.html.form.inputs.id.tariffs);
    tariffs.forEach(function (el) {
      $($tariffs).append(createTariffTemplate(el));
    });

    $('#' + taxiOrder.config.html.form.inputs.id.tariffs + ' option:first-child').attr('selected', true);
    $($tariffs).show();
  }, function (error) {
  });

});


function keyupAddress(event) {
  if (taxiOrder.isRequestBusy())
    return;
  var direction = $(event.target).closest('.direction_input').attr('data-direction');
  var autocompleteWrapper = document.getElementById(taxiOrder.config.html.form.inputs.id[direction].autocomplete.street);
  if (event.target.value.length < 3) {
    $(autocompleteWrapper).removeClass('active_autocomplete');
    return;
  }

  taxiOrder.requestDisable(0);
  taxiOrder.findGeoObjects(
          {part: event.target.value},
  function (geoObjects) {
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
    $(autocompleteWrapper).parents('.di_invisible').find('.dii_step').hide();
    $(autocompleteWrapper).parents('.di_invisible').find('.ds_1').show();

    autocompleteWrapper.addEventListener('click', function (event) {
      var autocompleteInput = document.getElementById(taxiOrder.config.html.form.inputs.id[direction].address.street);
      $(autocompleteInput).attr('value', event.target.innerText);
      $(autocompleteWrapper).parents('.direction_input').find('.dii_step').hide();
      $(autocompleteWrapper).parents('.direction_input').find('.ds_2').show();
      (direction == 'to') && $('.order_form').trigger('click');
    });

  }, function (error) {
  }
  );
  taxiOrder.requestEnable(taxiOrder.config.request.delay);
}

function calcCost(event) {
  var params = {from: '', to: ''};
  params.from = document.getElementById(taxiOrder.config.html.form.inputs.id.from.address.street).value.trim();
  params.to = document.getElementById(taxiOrder.config.html.form.inputs.id.to.address.street).value.trim();

  var houseFrom = document.getElementById(taxiOrder.config.html.form.inputs.id.from.address.house).value.trim();
  if (houseFrom) params.from += ' '+houseFrom;

  var houseTo = document.getElementById(taxiOrder.config.html.form.inputs.id.to.address.house).value.trim();
  if (houseTo) params.to += ' '+houseTo;

  if (!params.from || !params.to)
    return;

  taxiOrder.calcCost(params, function (result) {
    var $tariffs = document.getElementById(taxiOrder.config.html.form.inputs.id.tariffs);
    var tariffInfo = JSON.parse($($tariffs).find(':selected').attr('data-res'));
    if ('km' == tariffInfo.choose_km_or_min) {
      if (result.dist > tariffInfo.include_km_or_min)
        result.cost = 1 * tariffInfo.landing_cost + 1 * (result.dist - tariffInfo.include_km_or_min) * tariffInfo.km_or_min_cost;
    }
    else {
      if (result.time > tariffInfo.include_km_or_min)
        result.cost = 1 * tariffInfo.landing_cost + 1 * (result.time - tariffInfo.include_km_or_min) * tariffInfo.km_or_min_cost;
    }

    if (tariffInfo.minimal_cost > result.cost)
      result.cost = tariffInfo.minimal_cost;

    var lang = taxiOrder.config.phrases.currentLang;
    document.getElementById(taxiOrder.config.html.form.elements.id.result.calccost.label).innerText = taxiOrder.config.phrases[lang].calcCostTitle;
    document.getElementById(taxiOrder.config.html.form.elements.id.result.calccost.cost).innerText = result.cost + ' ' + taxiOrder.config.phrases[lang].calcCostCurrency;
    document.getElementById(taxiOrder.config.html.form.elements.id.result.calccost.time).innerText = result.time + ' ' + taxiOrder.config.phrases[lang].calcCostTime
    document.getElementById(taxiOrder.config.html.form.elements.id.result.calccost.dist).innerText = result.dist + ' ' + taxiOrder.config.phrases[lang].calcCostDist
    document.getElementById(taxiOrder.config.html.form.elements.id.result.calccost.sumcost).style.display = 'block';
  }, function (error) {
  });
}

function createOrder(event) {
  document.getElementById(taxiOrder.config.html.form.elements.id.result.info).innerHTML = '';
  taxiOrder.formParams = takeFormParams();
  taxiOrder.hasFormParams = true;
  taxiOrder.createOrder(taxiOrder.formParams, function (result) {
    var lang = taxiOrder.config.phrases.currentLang;
    $('.order_status').html(taxiOrder.config.phrases[lang].orderCreated.replace('{id}', result));
    $('.reject_order').remove();
    go_to_step(4);
  }, function (error) {
    $('#' + taxiOrder.config.html.form.elements.id.result.info).append(error.errors);
    taxiOrder.hasFormParams = false;
    console.log(error)
  });
  event.preventDefault();
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
    priorTime: time.trim(),
    phone: $('#'+taxiOrder.config.html.form.inputs.id.phone).attr('value').trim(),
    carType: $('#' + taxiOrder.config.html.form.inputs.id.tariffs).attr('value').trim(),
    tariffGroupId: $('#' + taxiOrder.config.html.form.inputs.id.tariffs).find('option:selected').attr('data-id').trim(),
    fromLat: '56.002657',
    fromLon: '92.792753',
    toLat: '56.007368',
    toLon: '92.808967',
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

function createTariffTemplate(el) {
  return "<option data-res='" + JSON.stringify(el, null, '') + "' id='tariff-'" + el.id + "' data-name='" + el.label + "' data-id='" + el.id + "' name='" + taxiOrder.config.html.form.inputs.name.tariffs + "'>"
          + el.label
          + "</option>";
}


function createGeoObjectTemplate(el) {
  if (el)
    return "<li data-res=" + JSON.stringify(el) + ">" + el.label + "</li>";

  var lang = taxiOrder.config.phrases.currentLang;
  return "<span>" + taxiOrder.config.phrases[lang].autocompleteNoResultsText + "</span>";
}