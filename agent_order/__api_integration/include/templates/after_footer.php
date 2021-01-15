<?php
/*
 * Часть кода футера для подгрузки ядра АПИ подсистемы
 */
?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/api_integration/assets/js_package_loader.php'; ?>

<script type="text/javascript">
    window.city = "<?
$APPLICATION->IncludeFile(SITE_DIR . "include/city.php", Array(), Array(
    "MODE" => "text",
    "SHOW_BORDER" => false
));
?>";
</script>

