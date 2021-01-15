$(document).ready(function() {

   

    function getTariffs() {
        var getTariffs = new TaxiMethod('findTariffs');
        getTariffs.tryCount = 1;
        console.log('Получение тарифов');
        getTariffs.successCallback = function(result) {
            if (result && result != "") {
                /* $('#tariff_travel').hide();
                 $('#tariff_travel').after('<select class="span12" id="tariffs"></select>');
                 $("#tariffs").append("<option value=''></option>");
                 $("label[for='FIELD_TIP']").text('Тариф');*/
                $('#tariff_travel').before('<select class="span12" id="tariff_travelNew"></select>');
                $('#tariff_travel').remove();
                $("#tariff_travelNew").attr("id", "tariff_travel");
                $("label[for='FIELD_TIP']").text('Тариф');
                $("#tariff_travel").append("<option value=''></option>");
                for (var i in result)
                {
                    var current = result[i];
                    var currentId = current['id'];
                    var currentLabel = current['label'];
                    $("#tariff_travel").append("<option value='" + currentId + "'>" + currentLabel + "</option>");

                }
            }


        };
        getTariffs.errorCallback = function() {
            console.log('Ошибка при выполнении запроса на получении тарифов!');
        };
        taxi.taxiClient.executeQuery(getTariffs);

    }

      /**
     * получение цены через ПО диспетчерских ДЛЯ Алладина
     * @returns {undefined}
     */
    function getCostFromApi() {
        if (needSendToValidate() === true) {
            console.log('Отправка даных для получения цены');
            var values = taxi.ordering.createOrderQuery();
            var callCostQuery = new TaxiMethod('callCost');
            callCostQuery.tryCount = 1;
            callCostQuery.params = values;
            console.log(values);
            callCostQuery.successCallback = function(result) {
                console.log('цена из ПО получена:' + result);
                if (result !== null) {
                    $('#cost_order').html(String(result));
                    taxi.lastCostfromApi = result;
                } else {

                }

            };
            callCostQuery.errorCallback = function() {
                console.log('Ошибка при выполнении запроса на получение цены!');


            };
            taxi.taxiClient.executeQuery(callCostQuery);

        }
    }



    function needSendToValidate() {

        if ((($('#FIELD_CITY_OTKUDA').val() !== "") && ($("#FIELD_FROM").val() !== "") &&
                ($('#FIELD_CITY_KUDA').val() !== "") && ($("#FIELD_TO").val() !== "")))
        {
            console.log('данные с форы необходимо отправить на получене цены');
            return true;
        }
        else {
            return false;
        }
    }



    $.each([$('#FIELD_CITY_OTKUDA'), $("#FIELD_FROM"), $("#FIELD_FROM_HOUSE"),
        $("#FIELD_FROM_HOUSING"), $('#FIELD_CITY_KUDA'), $("#FIELD_TO"),
        $("#FIELD_TO_HOUSE"), $("#FIELD_TO_HOUSING")], function() {
        $(this).on('blur', function() {
          //  getCostFromApi();
        });
    });


    // Округление цены для дня и ночи , если день то до 10, если ночь то до 5


   /* taxi.routeInfoFunction = function(res) {
        var len = res.length; // Длина маршрута
        var time = res.time; //время маршрута

        var timeLine = '';
//                    console.log(time);
        if (time + 1 < 1000 * 60) {
            timeLine = '<p>С учетом пробок '
                    + Math.round(time / 60) + ' мин.</p>';
        }
        // сохраним информацию о длине пути
        if (len > 0) {
            $('#list').attr('route-length', Math.round(len));
        }

        // расчет стоимости делаем через компонент расчета:
        var routeData = new TaxiCostRouteData();
        routeData.len = len;
        routeData.timeInMinutes = Math.round(time / 60);
        routeData.rawFrom = '';
        routeData.rawTo = '';

        //Время предзаказа
        if (taxi.ordering.useNewTemplate()) {
            routeData.priorTime = $('.order-taxi-when-date input').val();
        } else {
            routeData.priorTime = $('label[for=FIELD_DATA] ~ .controls input').val();
        }

        getCostFromApi();
        $('#list').html(
                '<div class="control-group rez"><label class="control-label"><span class="rect"></span></label><div class="controls"><p>Ехать '
                + Math.round(len / 1000) + ' км</p>'
                + timeLine
                + '<p>Стоимость проезда <span id="cost_order" style="color: white;">'
                + taxi.lastCostfromApi + '</span> рублей без доп. услуг.</p> <span id="range_travel" style="display: none;">'
                + Math.round(len / 1000) + '</span></div></div>'

                );

    };*/

   $('#tariff_travel').on('change', function() {
                 $('#cost_order').html(taxi.lastCostfromApi);        
            });
            


    
   // callCostFromApi();
   // getTariffs();
});
