<?$this->createFrame()->begin('');
$orderId = $_COOKIE['order_id'];
?>
<div class="span6">

    <span class="nav nav-tabs" id="myTab">
    </span>

    <? if (count($arResult["ERRORS"])): ?>
        <?= ShowError(implode("<br />", $arResult["ERRORS"])) ?>
    <? endif; ?>

    <? if (strlen($arResult["MESSAGE"]) > 0): ?>
        <?= ShowNote($arResult["MESSAGE"]) ?>
    <? endif; ?>


    <div class="order_steps step_1 <? if (!isset($orderId) && !($orderId)!==''): ?> active <? endif; ?>">
        <form name="iblock_add">
            <label class="left_label"><?=GetMessage("COLORS3_TAXI_FROM")?></label>
            <div style="position: relative; z-index: 10;">
                <input autocomplete="off" type="text" placeholder="<?=GetMessage("COLORS3_TAXI_ADDRESS")?>" id="FIELD_ADDRESS_FROM" class="input-style sp6_inp_street" style="width: 100%"/>
                <ul class="autocomplete_select" id="from_autocomplete"></ul>
            </div>
            <input style="min-width: 26%;" type="text" placeholder="<?=GetMessage("COLORS3_TAXI_PORCH")?>" id="FIELD_FROM_PORCH" class="input-style sp6_inp_pod"/>
            <textarea style="display: block;" id="FIELD_COMM" class="textarea-style sp6_txt" placeholder="<?=GetMessage("COLORS3_TAXI_COMMENT_FOR_DRIVER")?>"></textarea>
            <div style="display: none">
                <input type="text" placeholder="<?=GetMessage("COLORS3_TAXI_CITY")?>" id="FIELD_CITY_OTKUDA" class="input-style sp6_inp_city" value=""/>

                <div style="position: relative">
                    <input type="text" placeholder="<?=GetMessage("COLORS3_TAXI_STREET")?>" id="FIELD_FROM" class="input-style sp6_inp_street" value=""/>
                    <a title="<?=GetMessage("COLORS3_TAXI_MY_LOCATION")?>" href="#" id="find-me" class="find-me" style="cursor: pointer;float: right;margin:5px 5px 0 -24px;"></a>
                </div>
                <div class="FROM_HOUSE-group">
                    <input type="text" placeholder="<?=GetMessage("COLORS3_TAXI_HOUSE")?>" id="FIELD_FROM_HOUSE" class="input-style sp6_inp_house my-input" value=""/>
                    <input type="text" placeholder="<?=GetMessage("COLORS3_TAXI_HOUSING")?>" id="FIELD_FROM_HOUSING" class="input-style sp6_inp_corp" value=""/>
                    <input type="text" placeholder="<?=GetMessage("COLORS3_TAXI_PORCH")?>" id="FIELD_FROM_PORCH" class="input-style sp6_inp_pod" value=""/>
                    <input type="text" id="FIELD_FROM_LAT" value="">
                    <input type="text" id="FIELD_FROM_LON" value="">
                </div>
            </div>


            <label class="left_label"><?=GetMessage("COLORS3_TAXI_TO")?></label>
            <div style="position: relative; z-index: 9;">
                <input type="text" autocomplete="off" placeholder="<?=GetMessage("COLORS3_TAXI_ADDRESS")?>" id="FIELD_ADDRESS_TO" class="input-style sp6_inp_street" style="width: 100%"/>
                <ul class="autocomplete_select" id="to_autocomplete"></ul>
            </div>
            <div style="display: none">
                <input type="text" placeholder="<?=GetMessage("COLORS3_TAXI_CITY")?>" id="FIELD_CITY_KUDA" class="input-style sp6_inp_city" value=""/>

                <div style="position: relative">
                    <input type="text" placeholder="<?=GetMessage("COLORS3_TAXI_STREET")?>" id="FIELD_TO" class="input-style sp6_inp_street" value=""/>
                    <a title="<?=GetMessage("COLORS3_TAXI_MY_LOCATION")?>" href="#" id="find-me" class="find-me" style="cursor: pointer;float: right;margin:5px 5px 0 -24px;"></a>
                </div>
                <div class="TO_HOUSE-group">
                    <input type="text" placeholder="<?=GetMessage("COLORS3_TAXI_HOUSE")?>" id="FIELD_TO_HOUSE" class="input-style sp6_inp_house my-input" value=""/>
                    <input type="text" placeholder="<?=GetMessage("COLORS3_TAXI_HOUSING")?>" id="FIELD_TO_HOUSING" class="input-style sp6_inp_corp" value=""/>
                    <input type="text" placeholder="<?=GetMessage("COLORS3_TAXI_PORCH")?>" id="FIELD_TO_PORCH" class="input-style sp6_inp_pod" value=""/>
                    <input type="text" id="FIELD_TO_LAT" value="">
                    <input type="text" id="FIELD_TO_LON" value="">
                </div>
            </div>



            <div class="time_select">
                <select class="js-select" style="display: none;">
                    <option data-id="o01"><?=GetMessage("COLORS3_TAXI_FASTER_ORDER")?></option>
                    <option data-id="o02" id="pre_order_option"><?=GetMessage("COLORS3_TAXI_PRE_ORDER")?></option>
                </select>
                <div class="custom_datetimepicker_content" style="display: none;">
                    <select class="select-style">
                        <?for($i=0; $i<=3; $i++){
                            if($i!==0) echo '<option>';
                            else echo '<option selected>';
                            echo date("d.m.y", mktime(0, 0, 0, date("m"), date("d")+$i, date("Y")));
                            echo '</option>';
                        }?>
                    </select>
                    <input type="time" class="select-style" value="00:00"/>
                    <a class="button yellow"><?=GetMessage("IBLOCK_FORM_APPLY")?></a>
                </div>
                <input type="text" placeholder="<?=GetMessage("COLORS3_TAXI_TIME")?>" name="time" id="time" class="input-style sp6_inp_time" style="display: none;" />

            </div>

            <div class="sp6_fio">
                <label for=""><?=GetMessage("COLORS3_TAXI_CLIENT_NAME")?></label>
                <input type="text" id="FIELD_FIO" class="input-style"/>
            </div>
            <div class="sp6_tel">
                <label for=""><?=GetMessage("COLORS3_TAXI_CLIENT_PHONE")?></label>
                <input type="text" id="FIELD_TEL" class="input-style"/>
            </div>

            <div class="sp6_tar">
                <select id="tariff_travel" style="display: none;" class="js-select"></select>
                <div id="list" class="price">
                </div>
            </div>

            <div class="sp6_opt">
                <div class="js-select-wrap">
                    <a class="js-select-a"><span></span><?=GetMessage("COLORS3_TAXI_WISHES")?></a>
                    <div class="js-select-cont">
                        <a class="cls-sel"></a>
                        <ul id="additional">
                            <li><label><input data-cost="30" type="checkbox"/><?=GetMessage("COLORS3_TAXI_AIR_CONDITIONER")?></label></li>
                            <li><label><input data-cost="35" type="checkbox"/><?=GetMessage("COLORS3_TAXI_WITH_LUGGAGE")?></label></li>
                            <li><label><input data-cost="23" type="checkbox"/><?=GetMessage("COLORS3_TAXI_BABY_CHAIR")?></label></li>
                            <li><label><input data-cost="60" type="checkbox"/><?=GetMessage("COLORS3_TAXI_NO_SMOKING")?></label></li>
                        </ul>
                    </div>
                </div>
                <div id="options" class="checked_opt"></div>
            </div>
            <div style="clear: left; text-align: right;">
                <input class="btn rel call_me button yellow next" type="submit" name="iblock_submit" id="send_order_form" value="<?=GetMessage("COLORS3_TAXI_ZAKAZ_TAXI")?>" />
            </div>
        </form>
    </div>
    <div class="order_steps step_2" style="color: #fff;">
        <h4 style="margin-bottom: 10px;">Введите код подтверждения</h4>
        <p style="font-size: 14px;">На указанный телефон вам в течение 5-20 секунд придет бесплатное SMS сообщение с номером.</p>
        <p style="font-size: 14px;">Вам нужно этот номер ввести в нижестоящее поле. Это сделано для сокращения ложных вызовов.</p>
        <p style="font-size: 14px;">Спасибо за понимание.</p>
        <input class="input-style" type="text" placeholder="Код из SMS" id="smsCode" name="code" style="margin: 20px 0;">
        <div style="clear: left; text-align: right;">
            <input class="btn rel call_me button yellow next" type="submit" name="iblock_submit" id="go_to_step3" value="Отправить" />
        </div>
    </div>
    <div class="order_steps step_3 <? if (isset($orderId) && ($orderId)!==''): ?> active <? endif; ?>" style="color: #fff;">
        <h4 class="title_order_info">Информация по заказу №<span id="order_id">...</span></h4>
        <div style="margin-bottom: 40px;">
            <p>Статус заказа: <span id="order_status">...</span></p>
            <p>Стоимость: <span id="order_price">...</span></p>
        </div>
        <div class="result-buttons">
            <a href="#" id="reject_order" style="display: none;" class="button red">Отменить заказ</a>
            <a href="/" id="new_order" class="button yellow">Создать новый заказ</a>
        </div>
    </div>
</div>

