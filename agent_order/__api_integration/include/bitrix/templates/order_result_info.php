<?php
/*
 * Блок кода в шаблоне - если уже создан заказ - вывод суммарной информации и т.д.
 */
?>
<style>
    .result-buttons a{
        margin: 20px 0px;
        display: block;
    }

</style>
<div class="orderInfo">
    <h4 class="title_order_info">Заказ создан. Информация по заказy №<?= $orderId; ?></h4>
    <br>
    <p style="color: red;">Следите за статусом заказа на экране своего устройства (телефонный отзвон по вэб-заказам не производится)!</p>
    <br>
    <div id="result_order"></div>
    <div id="loading_info"><font>Загружаю...</font></div>
</div>
<script>
    jQuery(document).ready(function() {
        taxi.ordering.startOrderInfoUpdating('<?= $orderId; ?>');
    });
</script>
<div class="result-buttons">
<a href="#" id="reject_order" class="active" data-order_id="<?= $orderId; ?>" onclick="return false;"><font>Отменить заказ</font></a>
<a href="/" id="new_order" onclick="return false;"> <font>Создать новый заказ</font></a>
<a href="/reviews" id="send_review" style="display: none"><font>Оставить отзыв</font></a>
</div>

