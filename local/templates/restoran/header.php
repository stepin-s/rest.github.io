<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><?
CModule::IncludeModule('iblock');
use CFile;
?>
<?$cur_page = $curPage = $APPLICATION->GetCurPage(false);?>
<?$APPLICATION->SetCurPage($curPage)?>
<? $ar_city = CUriCity::get_instance()->get_current_element(); ?>
<? $ar_city_filter = array("PROPERTY_CITY" => $ar_city["id"]); ?>

<!DOCTYPE html>
<html lang="<?= CUriLang::get_instance()->get_element_code()?>" id="<?//= TaxiOrder::Config('html.elements.id.orderForm') ?>">
    <head>
        <title><? $APPLICATION->ShowTitle(); ?></title>
        <? $APPLICATION->ShowHead(); ?>

        <meta name="author" content="Студия Три Цвета <http://www.3colors.ru/>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <? use Bitrix\Main\Page\Asset; ?>

 <!-- jQuery 1.8 or later, 33 KB -->
        <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>  -->
		<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>

        <!-- slick slider -->
        <link rel="stylesheet" type="text/css" href="<?= SITE_TEMPLATE_PATH?>/restosite/slick/slick.css"/>
        <link rel="stylesheet" type="text/css" href="<?= SITE_TEMPLATE_PATH?>/restosite/slick/slick-theme.css"/>
        

        <!-- jQuery Modal -->
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	    <link href="<?= SITE_TEMPLATE_PATH?>/css/datepicker.min.css" rel="stylesheet">
        <link href="<?= SITE_TEMPLATE_PATH?>/restosite/styles/main.css" rel="stylesheet">

        
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" integrity="sha512-H9jrZiiopUdsLpg94A333EfumgUBpO9MdbxStdeITo+KEIMaNfHNvwyjjDJb+ERPaRS6DpyRlKbvPUasNItRyw==" crossorigin="anonymous" />

        <script src="//api-maps.yandex.ru/2.1/?load=package.full&lang=ru-RU"></script>
        <script type="text/javascript">
            window.geoservice = 'yandex';
        </script>


        <? // Asset::getInstance()->AddCss(SITE_TEMPLATE_PATH . '/css/screen.css') ?>
      
        <? //CJSCore::Init('jquery'); ?>
        <? CJSCore::Init();?>
        <? CUriLang::include_js() ?>
        <? CUriCity::include_js() ?>
        <?
        require_once $_SERVER["DOCUMENT_ROOT"] . "/include/taxiorder/TaxiOrder.php";
        TaxiOrder::init();
        ?>
	    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js" type="text/javascript"></script>
        <link rel="shortcut icon" type="image/ico" href="<?= SITE_TEMPLATE_PATH ?>/favicon.ico"/>
        <link rel="icon" href="<?= SITE_TEMPLATE_PATH ?>/favicon.ico" type="image/ico"/>

        <meta property="og:title" content="Демо-сайт заказа еды из ресторана" />
        <meta property="og:description" content="Демо-сайт заказа еды из ресторана" />
        <meta property="og:site_name" content="Демо-сайт Гутакс" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="rest.site.taxi3c.ru" />
        <meta property="og:image" content="<?= SITE_TEMPLATE_PATH?>/restosite/png/logo.png" />

    </head>
<body>
    <div id="panel" data-site="<?= SITE_DIR; ?>"><? $APPLICATION->ShowPanel(); ?></div>
    <?php $currentPage = $_SERVER['REQUEST_URI']; ?>

    <span style="visibility: hidden; position: absolute; z-index: -1;">
        <!-- SVG-Sprite -->
        <svg><defs/><symbol id="add" viewBox="0 0 25 25"><path d="M12.5 6v12m5.5-6H7" stroke="#2ABA90" stroke-width="2"/><circle cx="12.5" cy="12.5" r="12"/></symbol><symbol id="approved" viewBox="0 0 44 44"><circle cx="22" cy="22" r="22" fill="#FF9141"/><path d="M30.863 18.569h-4.446a.448.448 0 0 1-.457-.457v-4.111c0-1.858-1.949-1.95-2.893-1.888-.274.03-.457.243-.426.517.274 5.33-2.406 7.035-3.807 7.553a1.22 1.22 0 0 0-.792 1.157v6.67c0 2.436 2.436 2.436 2.436 2.436h8.527c1.219 0 2.437-.914 2.224-1.767-.061-.243 0-.487.09-.7l.366-.67c.153-.305.183-.64 0-.944a1.009 1.009 0 0 1 .03-.975l.488-.791a.89.89 0 0 0 .122-.731l-.183-.822c-.091-.335.03-.67.244-.914l.304-.305a1.03 1.03 0 0 0 .274-.578c.335-2.254-1.796-2.62-2.1-2.68zm-14.496 1.218h-2.101a1.06 1.06 0 0 0-1.066 1.066v8.496c0 .61.487 1.066 1.066 1.066h2.101a1.06 1.06 0 0 0 1.066-1.066v-8.496a1.08 1.08 0 0 0-1.066-1.066z" fill="#fff"/></symbol><symbol id="arrow-down" viewBox="0 0 14 8"><path d="M13 1L6.778 6 1 1" stroke-width="2"/></symbol><symbol id="arrow-left" viewBox="0 0 40 40"><circle r="20" transform="matrix(-1 0 0 1 20 20)" fill="#000"/><path d="M23 13l-6.5 7.5 6.5 7" stroke="#fff" stroke-width="2"/></symbol><symbol id="call" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8"/><path fill-rule="evenodd" clip-rule="evenodd" d="M5.052 5.874c.09-.317.609-.769.609-.769s.225-.18.519-.068c.315.09.496.407.721.973.248.588.248.565.136.769-.09.203-.429.565-.429.565.135.385 1.691 1.923 2.075 2.059 0 0 .36-.317.541-.43.203-.113.203-.09.767.136.586.226.88.407.992.724.09.316-.09.542-.09.542s-.428.52-.767.611c-1.781.52-5.615-3.302-5.074-5.112z" fill="#F8F8F8"/></symbol><symbol id="car" viewBox="0 0 18 12"><path d="M17.407 5.924l-1.646-.327-.771-2.294a2.045 2.045 0 0 0-1.928-1.362H11.75V.756A.766.766 0 0 0 10.98 0H.77A.766.766 0 0 0 0 .756v8.32c0 .428.36.756.771.756h.849C1.62 11.042 2.623 12 3.83 12c1.209 0 2.211-.983 2.211-2.168h5.914c0 1.21 1.003 2.168 2.211 2.168 1.234 0 2.211-.983 2.211-2.168h.849a.766.766 0 0 0 .771-.756v-2.42c.026-.353-.231-.656-.591-.732zM3.83 10.866c-.566 0-1.054-.454-1.054-1.034 0-.555.463-1.034 1.054-1.034a1.033 1.033 0 1 1 0 2.068zm7.92-5.471v-2.32h1.31a.88.88 0 0 1 .823.58l.592 1.74H11.75zm2.442 5.47c-.566 0-1.054-.453-1.054-1.033 0-.555.463-1.034 1.054-1.034.566 0 1.054.454 1.054 1.034a1.066 1.066 0 0 1-1.054 1.034z"/></symbol><symbol id="cart" viewBox="0 0 16 13"><path d="M13.13 3.5L10.365 0l-.909.735L11.641 3.5H3.23L5.434.735 4.525 0 1.76 3.5H0l1.953 8.644h11.139L15.045 3.5H13.13zm-.967 7.484H2.881L1.451 4.66h12.143l-1.43 6.324z" fill="#fff"/></symbol><symbol id="check" viewBox="0 0 10 11"><path d="M1 6l3.5 4L9 1" stroke="#000"/></symbol><symbol id="close" viewBox="0 0 11 10"><path d="M9.5 1L1 9m8.5 0L1 1" stroke="#fff" stroke-width="2"/></symbol><symbol id="delete" viewBox="0 0 18 20"><path d="M16.37 4h-2.889V3a3.06 3.06 0 0 0-.846-2.121A2.835 2.835 0 0 0 10.593 0H6.74c-.765 0-1.5.316-2.042.879A3.06 3.06 0 0 0 3.852 3v1H.963c-.255 0-.5.105-.681.293A1.02 1.02 0 0 0 0 5c0 .265.101.52.282.707.18.188.426.293.681.293h.963v10a4.08 4.08 0 0 0 1.128 2.828A3.78 3.78 0 0 0 5.778 20h5.778a3.78 3.78 0 0 0 2.723-1.172A4.079 4.079 0 0 0 15.407 16V6h.963c.256 0 .5-.105.681-.293A1.02 1.02 0 0 0 17.333 5c0-.265-.101-.52-.282-.707a.945.945 0 0 0-.68-.293zM5.778 3c0-.265.101-.52.282-.707A.945.945 0 0 1 6.74 2h3.853c.255 0 .5.105.68.293.181.187.283.442.283.707v1H5.778V3zm1.926 6v4c0 .265-.102.52-.282.707A.945.945 0 0 1 6.74 14a.945.945 0 0 1-.681-.293A1.02 1.02 0 0 1 5.778 13V9c0-.265.101-.52.282-.707A.945.945 0 0 1 6.74 8c.256 0 .501.105.682.293.18.187.282.442.282.707zm3.852 0v4c0 .265-.102.52-.282.707a.945.945 0 0 1-.681.293.945.945 0 0 1-.681-.293A1.02 1.02 0 0 1 9.63 13V9c0-.265.101-.52.282-.707a.945.945 0 0 1 .68-.293c.256 0 .5.105.681.293.181.187.283.442.283.707z"/></symbol><symbol id="done" viewBox="0 0 11 12"><path d="M1 4.6L4.75 10 10 1" stroke-width="2"/></symbol><symbol id="eco" viewBox="0 0 44 44"><circle cx="22" cy="22" r="22" fill="#2ABA90"/><path d="M17.182 29.49s-5.944-9.106 1.658-13.762c0 0 6.863-4.342 10.832-5.084-.177.407-2.705 2.528-1.793 5.352.38 1.174 4.465 12.307-8.954 14.5 0 0-3.006 2.646-3.526 2.907-.662.332-2.22-1.12-1.811-1.44.288-.225 3.81-2.114 3.594-2.473z" fill="#fff"/></symbol><symbol id="fb" viewBox="0 0 40 40"><circle cx="20" cy="20" r="19.5"/><path d="M17.238 20.618v-.5H14.5v-3.281h2.857v-3.2l-.007-.042c-.039-.222-.228-1.665 1.09-2.88 1.217-1.12 2.776-1.215 3.23-1.215.058 0 .095.001.106.002H24.5v3.03h-1.917c-.436 0-.854.104-1.163.396-.315.297-.432.708-.432 1.137v2.772h3.494l-.383 3.328h-3.111v10.15h-3.75v-9.697zm7.41-7.937v-.148.5-.352z"/></symbol><symbol id="geo" viewBox="0 0 12 16"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 6.366C12 2.854 9.299 0 6.026 0 2.701 0 0 2.854 0 6.366c0 2.936 2.703 6.411 5.026 8.503L6.162 16l1.297-1.217C9.68 12.703 12 8.897 12 6.366zM5.985 9.042c1.417 0 2.565-1.215 2.565-2.713S7.402 3.617 5.985 3.617C4.57 3.617 3.42 4.83 3.42 6.329c0 1.498 1.149 2.713 2.565 2.713z"/></symbol><symbol id="halal" viewBox="0 0 44 44"><circle cx="22" cy="22" r="22" fill="#127CCE"/><path d="M30.305 19.92l-2.2 1.1a1.1 1.1 0 1 0 .979 1.97l1.375-.693L31.9 24.2h-6.6v-7.7a1.1 1.1 0 0 0-2.2 0v7.7h-4.4a1.1 1.1 0 1 0 0 2.2h15.4a1.1 1.1 0 0 0 .88-1.76l-3.3-4.4a1.1 1.1 0 0 0-1.375-.32zM9.9 26.4h5.5a1.1 1.1 0 0 0 1.1-1.1v-8.8a1.1 1.1 0 0 0-2.2 0v7.7H9.9a1.1 1.1 0 0 0 0 2.2z" fill="#fff"/><path d="M19.448 17.655a1.1 1.1 0 0 0-.693 1.397l1.1 3.3a1.101 1.101 0 0 0 2.09-.692l-1.1-3.3a1.1 1.1 0 0 0-1.397-.704z" fill="#fff"/></symbol><symbol id="inst" viewBox="0 0 40 40"><circle cx="20" cy="20" r="19.5"/><rect x="11.5" y="11.5" width="17" height="17" rx="4.5"/><circle cx="20" cy="20" r="4"/><circle cx="25.63" cy="15.5" r="1.13" fill="#BBB"/></symbol><symbol id="locked" viewBox="0 0 10 14"><path d="M8.783 5.696h-.36l-.001-1.914C8.42 1.696 6.832-.002 4.879 0 2.927.002 1.34 1.699 1.34 3.785L1.343 5.7h-.128C.544 5.7 0 6.283 0 7v5.701C0 13.418.546 14 1.217 14l7.569-.005c.67 0 1.216-.581 1.214-1.299V6.994c0-.717-.546-1.298-1.217-1.298zM2.647 5.7l-.003-1.915c0-1.318 1.002-2.39 2.235-2.391 1.233 0 2.237 1.072 2.237 2.388l.003 1.914-4.472.004z" fill="#C4C4C4"/></symbol><symbol id="login-arrow" viewBox="0 0 13 7"><path d="M12 1L6.296 6 1 1" stroke="#2ABA90"/></symbol><symbol id="login" viewBox="0 0 15 15"><path d="M7.5 7a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zm7.5 8c-1.105-3.498-4.046-6-7.5-6-3.455 0-6.395 2.502-7.5 6" fill="#2ABA90"/></symbol><symbol id="menu-close" viewBox="0 0 16 16"><path d="M1.411-.003l14.142 14.142-1.414 1.414L-.003 1.411z"/><path d="M-.001 14.14L14.14-.001l1.414 1.415L1.413 15.555z"/></symbol><symbol id="menu" viewBox="0 0 18 16"><path d="M0 0h18v2H0zm0 7h18v2H0zm0 7h18v2H0z"/></symbol><symbol id="modal-close-dark" viewBox="0 0 21 20"><path d="M19.906 1l-19 17.882m19 0L.906 1" stroke-width="2"/></symbol><symbol id="modal-close" viewBox="0 0 21 20"><path d="M19.906 1l-19 17.882m19 0L.906 1" stroke-width="2"/></symbol><symbol id="new" viewBox="0 0 44 44"><circle cx="22" cy="22" r="22" fill="#FF6868"/><path d="M9.668 16.767H6.747v12.567H9.07v-8.8h.035l3.098 8.8h2.886V16.767h-2.323v8.607h-.035l-3.063-8.607zm14.207 0h-7.022v12.567h7.198v-2.077h-4.664v-3.38h4.224v-2.076h-4.224v-2.957h4.488v-2.077zm.712 0l2.288 12.567h2.834l1.408-9.117h.035l1.408 9.117h2.834l2.288-12.567h-2.43l-1.39 9.293h-.035l-1.478-9.293h-2.43l-1.477 9.293h-.036l-1.39-9.293h-2.429z" fill="#fff"/></symbol><symbol id="plus" viewBox="0 0 10 10"><path d="M0 4h10v2H0z"/><path d="M4 10V0h2v10z"/></symbol><symbol id="popular" viewBox="0 0 44 44"><circle cx="22" cy="22" r="22" fill="#FF9141"/><path d="M30.863 18.569h-4.446a.448.448 0 0 1-.457-.457v-4.111c0-1.858-1.949-1.95-2.893-1.888-.274.03-.457.243-.426.517.274 5.33-2.406 7.035-3.807 7.553a1.22 1.22 0 0 0-.792 1.157v6.67c0 2.436 2.436 2.436 2.436 2.436h8.527c1.219 0 2.437-.914 2.224-1.767-.061-.243 0-.487.09-.7l.366-.67c.153-.305.183-.64 0-.944a1.009 1.009 0 0 1 .03-.975l.488-.791a.89.89 0 0 0 .122-.731l-.183-.822c-.091-.335.03-.67.244-.914l.304-.305a1.03 1.03 0 0 0 .274-.578c.335-2.254-1.796-2.62-2.1-2.68zm-14.496 1.218h-2.101a1.06 1.06 0 0 0-1.066 1.066v8.496c0 .61.487 1.066 1.066 1.066h2.101a1.06 1.06 0 0 0 1.066-1.066v-8.496a1.08 1.08 0 0 0-1.066-1.066z" fill="#fff"/></symbol><symbol id="remove" viewBox="0 0 25 25"><path d="M18 12H7" stroke="#2ABA90" stroke-width="2"/><circle cx="12.5" cy="12.5" r="12"/></symbol><symbol id="sale" viewBox="0 0 44 44"><circle cx="22" cy="22" r="22" fill="#8FDB6B"/><path d="M15.879 17.516c0-.88.286-1.589.86-2.13.572-.54 1.34-.81 2.304-.81.977 0 1.748.27 2.314.81.567.535.85 1.26.85 2.178v.704c0 .878-.283 1.585-.85 2.119-.566.534-1.331.8-2.294.8-.957 0-1.729-.263-2.315-.79-.58-.534-.87-1.263-.87-2.188v-.693zm2.119.752c0 .338.094.615.283.83.19.208.45.312.782.312.683 0 1.025-.436 1.025-1.308v-.586c0-.339-.091-.616-.274-.83-.182-.222-.439-.332-.771-.332-.319 0-.573.107-.762.322-.189.208-.283.495-.283.86v.732zm4.639 7.246c0-.886.286-1.595.86-2.13.579-.533 1.347-.8 2.304-.8.97 0 1.741.264 2.314.791s.86 1.263.86 2.207v.684c0 .885-.284 1.595-.85 2.129-.566.533-1.335.8-2.305.8-.957 0-1.728-.263-2.314-.79-.58-.535-.87-1.264-.87-2.188v-.703zm2.11.752c0 .306.103.576.312.81a.994.994 0 0 0 .761.342c.632 0 .974-.3 1.026-.898l.01-1.006c0-.345-.095-.622-.284-.83-.188-.215-.446-.323-.771-.323-.313 0-.563.101-.752.303-.189.195-.29.462-.303.8v.802zm-5.06 1.67l-1.533-.772 6.944-11.113 1.533.771-6.944 11.114z" fill="#fff"/></symbol><symbol id="spicy" viewBox="0 0 44 44"><circle cx="22" cy="22" r="22" fill="#D4040A"/><path d="M28.455 21.116c-.561-.864-1.355-1.471-2.15-2.102-.84-.678-1.612-1.425-2.243-2.29-.023-.047-.07-.093-.093-.14-1.075-1.542-1.729-3.832-.724-5.584-.07.117-.491.234-.608.304-.303.14-.63.28-.934.467a9.308 9.308 0 0 0-1.8 1.355c-1.565 1.449-2.733 3.318-2.826 5.49-.024.818-.024 1.683.116 2.5.14.795.631 1.472.444 2.313-.023.117-.093.257-.21.304-.093.023-.187-.023-.28-.07-1.612-.864-2.477-3.575-1.893-5.257-.607 1.753-1.682 3.201-1.962 5.07a6.627 6.627 0 0 0 .327 3.435 7.876 7.876 0 0 0 1.822 2.78c.841.84 1.87 1.542 2.944 2.056.187.093.397.187.607.257.234.07.468.14.725.187 1.261.233 2.593.257 3.855.023a7.798 7.798 0 0 0 2.827-1.121 8.027 8.027 0 0 0 2.5-2.664c1.308-2.22.91-5.21-.444-7.313z" fill="#fff"/></symbol><symbol id="tw" viewBox="0 0 40 40"><circle cx="20" cy="20" r="19.5"/><path d="M29.205 13.915l-1.14 1.29 1.654-.479c.154-.044.306-.094.458-.147a7.831 7.831 0 0 1-1.453 1.428l-.207.156.008.259c.003.113.005.227.005.34 0 2.701-1.044 5.443-2.866 7.514-1.456 1.654-4.063 3.627-8.255 3.627a11.06 11.06 0 0 1-5.356-1.38c.072.002.145.002.217.002a8.09 8.09 0 0 0 4.326-1.237l1.027-.645-1.183-.266a3.794 3.794 0 0 1-2.68-2.274l.07.008.306-.927a3.82 3.82 0 0 1-1.85-3.027c.124.058.252.11.382.154l1.73.601-1.185-1.397a3.811 3.811 0 0 1-.51-4.149 11.486 11.486 0 0 0 7.718 3.989l.618.054-.079-.616a3.802 3.802 0 0 1 3.762-4.293c.991 0 1.95.393 2.656 1.084l.194.19.264-.059a8.093 8.093 0 0 0 1.964-.709 3.79 3.79 0 0 1-.595.91zm-15.746 8.142z"/></symbol><symbol id="user-arrow" viewBox="0 0 13 7"><path d="M12 1L6.296 6 1 1" stroke="#2ABA90"/></symbol><symbol id="user" viewBox="0 0 20 20"><path d="M10 8.667A2.333 2.333 0 1 0 10 4a2.333 2.333 0 0 0 0 4.667zM15 14c-.737-2.332-2.697-4-5-4s-4.263 1.668-5 4" fill="#2ABA90"/><circle cx="10" cy="10" r="9.5" stroke="#2ABA90"/></symbol><symbol id="vegetarian" viewBox="0 0 44 44"><circle cx="22" cy="22" r="22" fill="#2ABA90"/><path d="M17.182 29.49s-5.944-9.106 1.658-13.762c0 0 6.863-4.342 10.832-5.084-.177.407-2.705 2.528-1.793 5.352.38 1.174 4.465 12.307-8.954 14.5 0 0-3.006 2.646-3.526 2.907-.662.332-2.22-1.12-1.811-1.44.288-.225 3.81-2.114 3.594-2.473z" fill="#fff"/></symbol><symbol id="vk" viewBox="0 0 40 40"><circle cx="20" cy="20" r="19.5"/><path d="M25.219 26.029a38.682 38.682 0 0 0 2.406-.009c.397-.012.788-.09 1.159-.232a1.086 1.086 0 0 0 .618-.59 1.142 1.142 0 0 0-.05-.916 4.935 4.935 0 0 0-.44-.758 11.054 11.054 0 0 0-1.747-1.884 7.15 7.15 0 0 1-.733-.72.55.55 0 0 1-.125-.262.842.842 0 0 1 .16-.268c.25-.339.505-.674.76-1.008l.03-.037c.196-.258.393-.515.587-.775l.128-.172c.492-.658 1.001-1.34 1.318-2.152.13-.289.184-.606.155-.921-.057-.416-.409-.857-1.229-.857h-.013l-1.237.013c-.61.009-1.219.016-1.828.016h-.165c-.526 0-.898.243-1.138.742l-.224.465a38.666 38.666 0 0 1-.941 1.877 6.005 6.005 0 0 1-1.219 1.626 4.18 4.18 0 0 1-.077-1.217l.008-.324c.004-.255.019-.51.033-.765l.003-.043c.024-.42.048-.837.033-1.256-.022-.605-.178-1.255-1.17-1.482a4.446 4.446 0 0 0-1.184-.117c-.739.007-1.48.036-2.215.092a1.602 1.602 0 0 0-1.174.6.932.932 0 0 0-.207.737c.075.303.381.448.622.515.326.093.365.223.383.284.055.209.088.423.097.638.02.295.025.59.013.885-.006.154-.006.31-.005.464a3.637 3.637 0 0 1-.096 1.058 1.556 1.556 0 0 1-.266-.268 9.777 9.777 0 0 1-.509-.684 16.653 16.653 0 0 1-1.565-2.93c-.263-.629-.783-.972-1.445-.931-.274.006-.547.005-.819.004l-1.239-.004-.099-.001a4.133 4.133 0 0 0-.401.007c-.638.045-.925.319-1.053.54-.12.207-.209.555.038 1.043l.505 1.009.038.076c.435.872.87 1.744 1.339 2.6a17.285 17.285 0 0 0 2.065 3.066c.45.517.925 1.011 1.425 1.481a5.857 5.857 0 0 0 1.926 1.175c.538.218 1.1.372 1.674.46.505.06 1.014.082 1.522.067h.398c.863 0 1.326-.446 1.38-1.367.022-.383.043-.744.21-1.04.035-.065.067-.093.098-.066.2.125.382.274.543.445.178.18.35.367.521.554l.018.02c.217.235.433.47.662.694.53.518 1.113.788 1.738.803z"/></symbol></svg>
      </span>

<?if(!isset($_COOKIE['show_mobile_apps'])):?>
    <section class="mobile-download">
        <div class="container">
            <div class="mobile-download__inner">
                <a href="#" class="mobile-download__close">
                    <svg class="icon icon-ad-close">
                        <use xlink:href="#close"></use>
                    </svg>
                </a>
                <div class="mobile-download__logo" style="background-image: url(<?= SITE_TEMPLATE_PATH?>/restosite/png/app-logo.png);"></div>
                <div class="mobile-download__header">
                    А с приложениием  ещё удобнее
                </div>
                <a href="/apps/" class="mobile-download__link">Скачать</a>
            </div>
        </div>
    </section>
<?endif;?>

<?if (!strpos($currentPage, 'cart')):?>
    <header class="main-header">
        <div class="container">
            <div class="main-header__inner">
                <a href="/" class="logo" style="background-image: url(<?= SITE_TEMPLATE_PATH?>/restosite/png/logo.png); "></a>
                <a href="#" rel="modal:open"  class="city" style="cursor: default;"> 
					<!-- modal-city -->
                    <svg class="icon icon-geo">
                        <use xlink:href="#geo"></use>
                    </svg>
                    <span class="city__text ">Петропавловск-Камчатский</span>
                </a>
                <a href="tel:8 (495) 381-09-32" class="phone">
                    <svg class="icon icon-call">
                        <use xlink:href="#call"></use>
                    </svg>
                    <span class="phone__text">8 (495) 381-09-32</span>
                </a>
                <div class="delivery">
                    <svg class="icon icon-car">
                        <use xlink:href="#car"></use>
                    </svg>
                    <span class="delivery__text">Сегодня до 23:00</span>
                </div>
                <? global $USER;
                if ($USER->IsAuthorized()):?>
                <div class="user-menu__wrapper">
                    <a href="#" class="user-menu">
                        <svg class="icon icon-user">
                            <use xlink:href="#user"></use>
                        </svg>
                        <svg class="icon icon-user-arrow">
                            <use xlink:href="#user-arrow"></use>
                        </svg>
                    </a>
                    <ul class="user-menu__menu">
                        <li class="user-menu__item"><a href="/account/profile/">Профиль</a></li>
<!--                        <li class="user-menu__item"><a href="#">История </a></li>-->
                        <li class="user-menu__item"><a href="/logout/">Выход</a></li>
                    </ul>
                </div>
                <?else:?>
                <a href="#modal-login" rel="modal:open" class="login">Войти</a>
                <?endif;?>
            </div>
        </div>
    </header>
    <header class="mobile-header">
        <div class="container">
            <div class="mobile-header__inner">
                <a href="/" class="logo" style="background-image: url(<?= SITE_TEMPLATE_PATH?>/restosite/png/logo.png);"></a>
                <a href="#" class="mobile-menu-icon">
                    <svg class="icon icon-menu">
                        <use xlink:href="#menu"></use>
                    </svg>
                    <svg class="icon icon-menu-close" >
                        <use xlink:href="#menu-close"></use>
                    </svg>
                </a>
                <div class="mobile-menu">
                    <ul class="mobile-menu__list">
                        <li>
                            <a href="#" class="city">
                                <svg class="icon icon-geo">
                                    <use xlink:href="#geo"></use>
                                </svg>
                                <span class="city__text">Петропавловск-Камчатский</span>
                            </a>
                        </li>
                        <li>
                            <a href="tel:8 (495) 381-09-32" class="phone">
                                <svg class="icon icon-call">
                                    <use xlink:href="#call"></use>
                                </svg>
                                <span class="phone__text">8 (495) 381-09-32</span>
                            </a>
                        </li>
                        <li>
                            <div class="delivery">
                                <svg class="icon icon-car">
                                    <use xlink:href="#car"></use>
                                </svg>
                                <span class="delivery__text">Сегодня до 23:00</span>
                            </div>
                        </li>
						<? global $USER;
                if ($USER->IsAuthorized()):?>
                <div class="user-menu__wrapper">
                    <a href="#" class="user-menu">
                        <svg class="icon icon-user">
                            <use xlink:href="#user"></use>
                        </svg>
                        <svg class="icon icon-user-arrow">
                            <use xlink:href="#user-arrow"></use>
                        </svg>
                    </a>
                    <ul class="user-menu__menu">
                        <li class="user-menu__item"><a href="/account/profile/">Профиль</a></li>
                        <li class="user-menu__item"><a href="/logout/">Выход</a></li>
                    </ul>
                </div>
                <?else:?>
                    <li>
                            <a href="#modal-login" rel="modal:open" class="mobile-login">
                                <svg class="icon icon-login">
                                    <use xlink:href="#login"></use>
                                </svg>
                                <span>Войти</span>
                            </a>
                        </li>
                <?endif;?>
                        
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <nav class="main-nav">
        <div class="container">
            <a href="/" class="logo" style="background-image: url(<?= SITE_TEMPLATE_PATH?>/restosite/png/logo.png);"></a>
            <div class="main-nav__inner">
                <ul class="main-nav__left">
                    <?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"top_menu", 
	array(
		"ROOT_MENU_TYPE" => "top",
		"CHILD_MENU_TYPE" => "top",
		"MAX_LEVEL" => "3",
		"USE_EXT" => "Y",
		"DEPTH_LEVEL" => "2",
		"COMPONENT_TEMPLATE" => "top_menu",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
                </ul>
                <a href="/cart_new/" class="button-cart">
                    <svg class="icon icon-cart">
                        <use xlink:href="#cart"></use>
                    </svg>
                    <span class="button-cart__text">Корзина</span>
                    <span class="button-cart__number">0</span>
                </a>
            </div>
        </div>
    </nav>
<?else:?>
    <header class="main-header cart">
    <div class="container">
        <div class="main-header__inner">
            <a href="/" class="logo" style="background-image: url(<?= SITE_TEMPLATE_PATH?>/restosite/png/logo.png);"></a>

            <div class="cart-header">
                <div class="cart-header__item js-header-step active" data-step="0">
                    <i><span>1</span><img src="/local/templates/restoran/restosite/svg/green-checkb.svg" style="display: none;"></i>

                    <span>Корзина</span>
                </div>
                <div class="cart-header__item js-header-step" data-step="1">
                    <i><span>2</span><img src="/local/templates/restoran/restosite/svg/green-checkb.svg" style="display: none;"></i>

                    <span>Оформление</span>
                </div>
                <div class="cart-header__item js-header-step" data-step="2">
                    <i><span>3</span><img src="/local/templates/restoran/restosite/svg/green-checkb.svg" style="display: none;"></i>

                    <span>Заказ принят</span>
                </div>
            </div>
        </div>
    </div>
</header>
<header class="mobile-header">
    <div class="container">
        <div class="mobile-header__inner">
            <a href="/" class="logo" style="background-image: url(<?= SITE_TEMPLATE_PATH?>/restosite/png/logo.png);"></a>
	        <a href="#" class="mobile-menu-icon">
		        <svg class="icon icon-menu">
			        <use xlink:href="#menu"></use>
		        </svg>
		        <svg class="icon icon-menu-close" >
			        <use xlink:href="#menu-close"></use>
		        </svg>
	        </a>
	        <div class="mobile-menu">
		        <ul class="mobile-menu__list">
			        <li>
				        <a href="#" class="city">
					        <svg class="icon icon-geo">
						        <use xlink:href="#geo"></use>
					        </svg>
					        <span class="city__text">Петропавловск-Камчатский</span>
				        </a>
			        </li>
			        <li>
				        <a href="tel:8 (495) 381-09-32" class="phone">
					        <svg class="icon icon-call">
						        <use xlink:href="#call"></use>
					        </svg>
					        <span class="phone__text">8 (495) 381-09-32</span>
				        </a>
			        </li>
			        <li>
				        <div class="delivery">
					        <svg class="icon icon-car">
						        <use xlink:href="#car"></use>
					        </svg>
					        <span class="delivery__text">Сегодня до 23:00</span>
				        </div>
			        </li>
			        <li>
				        <a href="#modal-login" rel="modal:open" class="mobile-login">
					        <svg class="icon icon-login">
						        <use xlink:href="#login"></use>
					        </svg>
					        <span>Войти</span>
				        </a>
			        </li>
		        </ul>
	        </div>
        </div>
    </div>
</header>
    <?endif;?>
        <div style="opacity: 0; height: 0; width: 0; position: absolute;">
        	<?CUriLang::show_elements()?>
        	<?CUriCity::show_elements()?> 
        </div> 
	
    	<script>
    		window.order = "";
    		window.crew = "";
    		window.source = "";
    		window.order_city = "";			
    	</script>
    	
    	<div class="wrapper<?= ($curPage != SITE_DIR."index.php") ? '_second' : ''?><?= ($curPage == SITE_DIR."order/index.php") ? ' nobg' : ''?>">
    		