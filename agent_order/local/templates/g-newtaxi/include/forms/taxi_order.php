<?
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/taxiorder/TaxiOrder.php";
TaxiOrder::init();
?>
<div id="form_order" style="padding-bottom: 20px;">
    <? $this->createFrame()->begin('');
    $orderId = $_COOKIE['order_id'];
    ?>
    <div class="span6">
        <div id="order-result">
            <div id="result-message"></div>
    <span class="nav nav-tabs" id="myTab">
    </span>

            <? if (count($arResult["ERRORS"])): ?>
                <?= ShowError(implode("<br />", $arResult["ERRORS"])) ?>
            <? endif; ?>

            <? if (strlen($arResult["MESSAGE"]) > 0): ?>
                <?= ShowNote($arResult["MESSAGE"]) ?>
            <? endif; ?>

            <div class="order_steps step_1 <? if (!isset($orderId) && !($orderId) !== ''): ?> active <? endif; ?>">
                <form name="iblock_add">
                    <label class="left_label">Откуда</label>
                    <div style="position: relative; z-index: 10;" class="direction_input" data-direction="from">
                        <input autocomplete="off" type="text" placeholder="Адрес"
                               id="<?= TaxiOrder::Config('html.form.inputs.id.from.address.street') ?>"
                               class="input-style sp6_inp_street" style="width: 100%"/>
                        <ul class="autocomplete_select"
                            id="<?= TaxiOrder::Config('html.form.inputs.id.from.autocomplete.street') ?>"></ul>
                    </div>
                    <input style="min-width: 26%;" type="text" placeholder="Дом"
                           id="<?= TaxiOrder::Config('html.form.inputs.id.from.address.house') ?>"
                           class="input-style sp6_inp_pod"/>
                    <input style="min-width: 26%;" type="text" placeholder="Подъезд"
                           id="<?= TaxiOrder::Config('html.form.inputs.id.from.address.porch') ?>"
                           class="input-style sp6_inp_pod"/>
                <textarea style="display: block;" id="<?= TaxiOrder::Config('html.form.inputs.id.from.comment') ?>"
                          class="textarea-style sp6_txt"
                          placeholder="Уточнение для водителя"></textarea>
                    <div style="display: none">
                        <input type="text" placeholder="Город"
                               id="<?= TaxiOrder::Config('html.form.inputs.id.from.address.city') ?>"
                               class="input-style sp6_inp_city" value=""/>

                        <div style="position: relative">
                            <input type="text" placeholder="Улица"
                                   id="<?= TaxiOrder::Config('html.form.inputs.id.from.address.street') ?>"
                                   class="input-style sp6_inp_street" value=""/>
                            <a title="Определить моё местоположение" href="#" id="find-me"
                               class="find-me"
                               style="cursor: pointer;float: right;margin:5px 5px 0 -24px;"></a>
                        </div>
                        <div class="FROM_HOUSE-group">
                            <input type="text" placeholder="Дом"
                                   id="<?= TaxiOrder::Config('html.form.inputs.id.from.address.house') ?>"
                                   class="input-style sp6_inp_house my-input" value=""/>
                            <input type="text" placeholder="Корп./стр."
                                   id="<?= TaxiOrder::Config('html.form.inputs.id.from.address.housing') ?>"
                                   class="input-style sp6_inp_corp" value=""/>
                            <input type="text" placeholder="Подъезд"
                                   id="<?= TaxiOrder::Config('html.form.inputs.id.from.address.porch') ?>"
                                   class="input-style sp6_inp_pod" value=""/>
                            <input type="text" id="FIELD_FROM_LAT" value="">
                            <input type="text" id="FIELD_FROM_LON" value="">
                        </div>
                    </div>


                    <label class="left_label">Куда</label>
                    <div style="position: relative; z-index: 9;"  class="direction_input" data-direction="to">
                        <input type="text" autocomplete="off" placeholder="Адрес"
                               id="<?= TaxiOrder::Config('html.form.inputs.id.to.address.street') ?>"
                               class="input-style sp6_inp_street" style="width: 100%"/>
                        <ul class="autocomplete_select"
                            id="<?= TaxiOrder::Config('html.form.inputs.id.to.autocomplete.street') ?>"></ul>
                    </div>
                    <input type="text" placeholder="Дом"
                           id="<?= TaxiOrder::Config('html.form.inputs.id.to.address.house') ?>"
                           class="input-style sp6_inp_house my-input" value=""/>
                    <div style="display: none">
                        <input type="text" placeholder="Город"
                               id="<?= TaxiOrder::Config('html.form.inputs.id.to.address.city') ?>"
                               class="input-style sp6_inp_city" value=""/>

                        <div style="position: relative">
                            <input type="text" placeholder="Улица"
                                   id="<?= TaxiOrder::Config('html.form.inputs.id.to.address.street') ?>"
                                   class="input-style sp6_inp_street" value=""/>
                            <a title="Определить моё местоположение" href="#" id="find-me"
                               class="find-me"
                               style="cursor: pointer;float: right;margin:5px 5px 0 -24px;"></a>
                        </div>
                        <div class="TO_HOUSE-group">
                            <input type="text" placeholder="Дом"
                                   id="<?= TaxiOrder::Config('html.form.inputs.id.to.address.house') ?>"
                                   class="input-style sp6_inp_house my-input" value=""/>
                            <input type="text" placeholder="Корп./стр."
                                   id="<?= TaxiOrder::Config('html.form.inputs.id.to.address.housing') ?>"
                                   class="input-style sp6_inp_corp" value=""/>
                            <input type="text" placeholder="Подъезд"
                                   id="<?= TaxiOrder::Config('html.form.inputs.id.to.address.porch') ?>"
                                   class="input-style sp6_inp_pod" value=""/>
                            <input type="text" id="FIELD_TO_LAT" value="">
                            <input type="text" id="FIELD_TO_LON" value="">
                        </div>
                    </div>


                    <div class="time_select">
                        <select class="js-select" style="display: none;">
                            <option data-id="o01">Подать такси как можно скорее</option>
                            <option data-id="o02"
                                    id="pre_order_option">Предварительный заказ
                            </option>
                        </select>
                        <div class="custom_datetimepicker_content" style="display: none;">
                            <select class="select-style">
                                <? for ($i = 0; $i <= 3; $i++) {
                                    if ($i !== 0) echo '<option>';
                                    else echo '<option selected>';
                                    echo date("d.m.y", mktime(0, 0, 0, date("m"), date("d") + $i, date("Y")));
                                    echo '</option>';
                                } ?>
                            </select>
                            <input type="time" class="select-style" value="00:00"/>
                            <a class="button yellow">Применить</a>
                        </div>
                        <input type="text" placeholder="Время" name="time"
                               id="<?= TaxiOrder::Config('html.form.inputs.id.time') ?>"
                               class="input-style sp6_inp_time" style="display: none;"/>

                    </div>

                    <div class="sp6_fio">
                        <label for="">ФИО</label>
                        <input type="text" id="<?= TaxiOrder::Config('html.form.inputs.id.clientname') ?>"
                               class="input-style"/>
                    </div>
                    <div class="sp6_tel">
                        <label for="">Телефон</label>
                        <input type="text" id="<?= TaxiOrder::Config('html.form.inputs.id.phone') ?>"
                               class="input-style"/>
                    </div>

                    <div class="sp6_tar">
                        <select id="<?= TaxiOrder::Config('html.form.inputs.id.tariffs') ?>" style="display: none;"
                                class="js-select"></select>
                        <div id="list" class="price">
                        </div>
                    </div>

                    <div class="sp6_opt">
                        <div class="js-select-wrap">
                            <a class="js-select-a"><span></span>Пожелания</a>
                            <div class="js-select-cont">
                                <a class="cls-sel"></a>
                                <ul id="additional">
                                    <li><label><input data-cost="30"
                                                      type="checkbox"/>Кондиционер
                                        </label></li>
                                    <li><label><input data-cost="35"
                                                      type="checkbox"/>С собой большой багаж
                                        </label></li>
                                    <li><label><input data-cost="23"
                                                      type="checkbox"/>Детское кресло
                                        </label>
                                    </li>
                                    <li><label><input data-cost="60"
                                                      type="checkbox"/>Некурящий салон
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div id="options" class="checked_opt"></div>
                    </div>
                    <div style="clear: left; text-align: right;">
                        <input class="btn rel call_me button yellow next" type="submit" name="iblock_submit"
                               id="<?= TaxiOrder::Config('html.form.buttons.id.createorder') ?>"
                               value="Заказать такси"/>
                    </div>
                </form>
            </div>
            <div class="order_steps step_2" style="color: #fff;">
                <h4 style="margin-bottom: 10px;">Введите код подтверждения</h4>
                <p style="font-size: 14px;">На указанный телефон вам в течение 5-20 секунд придет бесплатное SMS
                    сообщение с
                    номером.</p>
                <p style="font-size: 14px;">Вам нужно этот номер ввести в нижестоящее поле. Это сделано для сокращения
                    ложных вызовов.</p>
                <p style="font-size: 14px;">Спасибо за понимание.</p>
                <input class="input-style" type="text" placeholder="Код из SMS" id="smsCode" name="code"
                       style="margin: 20px 0;">
                <div style="clear: left; text-align: right;">
                    <input class="btn rel call_me button yellow next" type="submit" name="iblock_submit"
                           id="go_to_step3"
                           value="Отправить"/>
                </div>
            </div>
            <div class="order_steps step_3 <? if (isset($orderId) && ($orderId) !== ''): ?> active <? endif; ?>"
                 style="color: #fff;">
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
    </div>


    <div class="span6 yamap ac" style="position: relative;">
        <div class="pokazat" style="text-align: center;">
            <a class="pokaz_karty active"><?= tr("SHOW_ON_MAP"); ?></a>
        </div>
        <div class="search-car"><i></i></div>
        <div id="<?= TaxiOrder::Config('html.form.elements.id.map') ?>"
             style="width: 100%; height: 400px; z-index: 2;"></div>
        <div id="error"></div>
    </div>
</div>