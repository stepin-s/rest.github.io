<?
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/taxiorder/TaxiOrder.php";
TaxiOrder::init();
?>
<style>
    #result-message .err {
        color: red;
        text-align: center;
        margin-bottom: 20px;
    }
</style>
<div class="span6">
    <div id="result-message"></div>
    <span class="nav nav-tabs" id="myTab"></span>

    <? if (count($arResult["ERRORS"])): ?>
        <?= ShowError(implode("<br />", $arResult["ERRORS"])) ?>
    <? endif; ?>

    <? if (strlen($arResult["MESSAGE"]) > 0): ?>
        <?= ShowNote($arResult["MESSAGE"]) ?>
    <? endif; ?>

    <?
    $orderId = $_COOKIE['api_order_id'];
    if (isset($orderId) && ($orderId) !== ''):
        /*
         * Заказ уже создан: вывод информации по заказу
         */
        ?>
    <? else: ?>
        <div class="order_steps step_1 <? if (!isset($orderId) && !($orderId) !== ''): ?> active <? endif; ?>">
            <form name="iblock_add" id="<?= TaxiOrder::Config('html.elements.id.orderForm') ?>"
                  data-lang="<?= TaxiOrder::getCurrentLang() ?>">

                <div style="height:10px;"></div>
                <label for="" style="top:22px;" class="left_label">Тип авто</label>
                <div style="float: right;" class="sp6_opt" id="<?= TaxiOrder::Config('html.inputs.id.wishes') ?>">
                    <div class="js-select-wrap">
                        <a class="js-select-a" style="text-align:right"><span></span><?= TaxiOrder::Phrase('wishes') ?></a>
                        <div class="js-select-cont">
                            <a class="cls-sel"></a>
                            <ul>
                                <li><label><input data-id="1" type="checkbox"/>Кондиционер</label></li>
                                <li><label><input data-id="2" type="checkbox"/>С собой большой багаж</label></li>
                                <li><label><input data-id="3" type="checkbox"/>Детское кресло</label></li>
                                <li><label><input data-id="4" type="checkbox"/>Некурящий салон</label></li>
                            </ul>
                        </div>
                    </div>
                    <div id="options" class="checked_opt"></div>
                </div>
                <div class="sp6_tar" id="<?= TaxiOrder::Config('html.inputs.id.tariffs') ?>" class="js-select"
                     style="display:none;">
                    <select class="js-select" style="display:none;">
                    </select>
                    <div class="js-select-wrap">
                        <a class="js-select-a"><span></span></a>
                        <div class="js-select-cont">
                            <ul>
                            </ul>
                        </div>
                    </div>
                    <div id="list" class="price"></div>
                </div>

                <div class="direction" data-direction="from">
                    <label class="left_label">Откуда</label>
                    <div style="position: relative">
                        <input
                            type="text"
                            placeholder="<?= TaxiOrder::Phrase('address') ?>"
                            id="<?= TaxiOrder::Config('html.inputs.id.from.street') ?>"
                            style="width:100%"
                            class="input-style sp6_inp_street"
                            autocomplete="off"
                        />
                        <ul class="autocomplete_select"
                            id="<?= TaxiOrder::Config('html.inputs.id.from.autocomplete') ?>"></ul>
                        <a title="<?= TaxiOrder::Phrase('findMe') ?>" href="#"
                           id="<?= TaxiOrder::Config('html.elements.id.findMe') ?>" class="find-me"
                           style="display:none; cursor: pointer;float: right;margin:5px 5px 0 -24px;"></a>
                    </div>
                    <!--input type="text" placeholder="<?= TaxiOrder::Phrase('house') ?>"
                               id="<?= TaxiOrder::Config('html.inputs.id.from.house') ?>"
                               class="input-style sp6_inp_house my-input"/>
                        <input type="text" placeholder="<?= TaxiOrder::Phrase('housing') ?>"
                               id="<?= TaxiOrder::Config('html.inputs.id.from.housing') ?>"
                               class="input-style sp6_inp_corp"/-->
                    <input style="min-width: 26%;" type="text" placeholder="<?= TaxiOrder::Phrase('porch') ?>"
                           id="<?= TaxiOrder::Config('html.inputs.id.from.porch') ?>"
                           class="input-style sp6_inp_pod"/>
                        <textarea style="display:block" id="<?= TaxiOrder::Config('html.inputs.id.from.comment') ?>"
                                  class="textarea-style sp6_txt"
                                  placeholder="<?= TaxiOrder::Phrase('comment') ?>"></textarea>

                </div>
                <div class="direction" data-direction="to">
                    <label class="left_label"><?= TaxiOrder::Phrase('directionTo') ?></label>

                    <div style="position: relative">
                        <input
                            style="width:100%"
                            type="text"
                            placeholder="<?= TaxiOrder::Phrase('address') ?>"
                            id="<?= TaxiOrder::Config('html.inputs.id.to.street') ?>"
                            class="input-style sp6_inp_street"
                            autocomplete="off"
                        />
                        <ul class="autocomplete_select"
                            id="<?= TaxiOrder::Config('html.inputs.id.to.autocomplete') ?>"></ul>
                    </div>

                    <!--div class="TO_HOUSE-group">
                        <input type="text" placeholder="<?= TaxiOrder::Phrase('house') ?>"
                               id="<?= TaxiOrder::Config('html.inputs.id.to.house') ?>"
                               class="input-style sp6_inp_house"/>
                        <input type="text" placeholder="<?= TaxiOrder::Phrase('housing') ?>"
                               id="<?= TaxiOrder::Config('html.inputs.id.to.housing') ?>"
                               class="input-style sp6_inp_corp"/>
                        <input type="text" placeholder="<?= TaxiOrder::Phrase('porch') ?>"
                               id="<?= TaxiOrder::Config('html.inputs.id.to.porch') ?>"
                               class="input-style sp6_inp_pod"/>
                    </div-->
                </div>

                <div class="time_select">
                    <select class="js-select" style="display:none;">
                        <option data-id="o01"><?= TaxiOrder::Phrase('orderTimeNow') ?></option>
                        <option data-id="o02"><?= TaxiOrder::Phrase('orderTimeLater') ?></option>
                    </select>
                    <div class="js-select-wrap">
                        <a class="js-select-a"><?= TaxiOrder::Phrase('orderTimeNow') ?><span></span></a>
                        <div class="js-select-cont">
                            <ul>
                                <li><a data-href="o01"><?= TaxiOrder::Phrase('orderTimeNow') ?></a></li>
                                <li><a class="order-later"
                                       data-href="o02"><?= TaxiOrder::Phrase('orderTimeLater') ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <input type="text" placeholder="<?= TaxiOrder::Phrase('time') ?>" name="time"
                           id="<?= TaxiOrder::Config('html.inputs.id.orderTime') ?>" class="input-style sp6_inp_time"
                           onclick="BX.calendar({node:this, field:'time', form: 'iblock_add', bTime: true, currentTime: '1421227608', bHideTime: false});"
                           style="float: left; display:none;"/>
                    <div style="display:none">
                        <?
                        $APPLICATION->IncludeComponent(
                            'bitrix:main.calendar', '', array(
                            'FORM_NAME' => 'iblock_add',
                            'INPUT_NAME' => "time",
                            'INPUT_VALUE' => $value,
                            'INPUT_CLASS' => 'input-style sp6_inp_time',
                            'SHOW_TIME' => 'Y'
                        ), null, array('HIDE_ICONS' => 'Y')
                        );
                        ?>
                    </div>
                </div>

                <div class="sp6_fio">
                    <label for=""><?= TaxiOrder::Phrase('userName') ?></label>
                    <input type="text" id="<?= TaxiOrder::Config('html.inputs.id.userName') ?>" class="input-style"/>
                </div>
                <div class="sp6_tel">
                    <label for=""><?= TaxiOrder::Phrase('userPhone') ?></label>
                    <input type="text" id="<?= TaxiOrder::Config('html.inputs.id.userPhone') ?>" class="input-style"/>
                </div>
                <div class="result-cost" style="color:white; font-size: 14px; display:none">
                    Расстояние: <span class="dist"></span><br>
                    Время: <span class="time"></span><br>
                    Примерная стоимость: <span class="cost"></span>
                </div>
                <div style="clear: left; text-align: right;">
                    <input class="btn rel call_me button yellow next" type="submit" name="iblock_submit"
                           id="<?= TaxiOrder::Config('html.buttons.id.createOrder') ?>"
                           value="<?= TaxiOrder::Phrase('createOrder') ?>"/>
                </div>
            </form>
        </div>
        <div class="order_steps step_2" style="color: #fff;">
            <h4 style="margin-bottom: 10px;">Введите код подтверждения</h4>
            <p style="font-size: 14px;">На указанный телефон вам в течение 5-20 секунд придет бесплатное SMS сообщение с
                номером.</p>
            <p style="font-size: 14px;">Вам нужно этот номер ввести в нижестоящее поле. Это сделано для сокращения
                ложных вызовов.</p>
            <p style="font-size: 14px;">Спасибо за понимание.</p>
            <input class="input-style" type="text" placeholder="Код из SMS"
                   id="<?= TaxiOrder::Config('html.inputs.id.smsCode') ?>" name="code"
                   style="margin: 20px 0;width:150px">
            <div style="clear: left; text-align: right;">
                <div class="result-sms" style="float: left;">
                    <span class="error" style="color:red"></span>
                </div>
                <input class="btn rel call_me button yellow next" type="submit" name="iblock_submit"
                       id="<?= TaxiOrder::Config('html.buttons.id.smsCode') ?>"
                       value="Отправить"/>
            </div>
        </div>
        <div class="order_steps step_3 <? if (isset($orderId) && ($orderId) !== ''): ?> active <? endif; ?>"
             style="color: #fff;">
            <h4 class="title_order_info">Информация по заказу №<span id="order_id">...</span></h4>
            <div style="margin-bottom: 40px;">
                <p>Статус заказа: <span id="order_status">...</span></p>
                <p style="display:none">Стоимость: <span id="order_price">...</span></p>
                <div id="order_car" style="display:none">
                    <p id="order_car_mark">Автомобиль: <span>...</span></p>
                    <p id="order_car_time">Подъедет через: <span>...</span></p>
                    <p id="order_driver">Водитель: <span>...</span></p>
                </div>
            </div>
            <div class="result-buttons">
                <a id="<?= TaxiOrder::Config('html.buttons.id.rejectOrder') ?>" style="display: none;"
                   class="button red">Отменить заказ</a>
                <a href="/" id="new_order" class="button yellow">Создать новый заказ</a>
            </div>
        </div>
    <?php endif; ?>
</div>
<div class="span6 yamap ac" style="position: relative;">
    <div class="pokazat" style="text-align: center;">
        <a class="pokaz_karty active"><?= tr("SHOW_ON_MAP"); ?></a>
    </div>
    <div class="search-car"><i></i></div>
    <div id="<?= TaxiOrder::Config('html.elements.id.map') ?>"
         style="width: 100%; height: 400px; z-index: 2;"></div>
    <div id="error"></div>
</div>