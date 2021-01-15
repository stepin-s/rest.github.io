</div>
</div>
<footer class="footer-top">
    <div class="container">
        <div class="footer__first">
            <a href="#" class="logo" style="background-image: url(<?= SITE_TEMPLATE_PATH?>/restosite/png/logo.png);"></a>
            <!-- <div class="lang">
                <a href="#">
                    <span>Русский</span>
                    <svg class="icon icon-arrow-down">
                        <use xlink:href="#arrow-down"></use>
                    </svg>
                </a>
            </div> -->
        </div>
        <div class="footer__second">
            <div class="footer__apps">
                <div class="footer__header">
                    Скачайте приложение
                </div>
                <ul class="footer__apps-list">
                    <li>
                        <a href="https://play.google.com/store/apps/details?id=com.gootax.demorestaurant" class="get-app__app google" target="_blank" rel="noopener"></a>
                    </li>
                    <li>
                        <a href="https://apps.apple.com/ru/app/id1524003806" class="get-app__app appstore" target="_blank" rel="noopener"></a>
                    </li>
                </ul>
            </div>
            <div class="footer__links">
                <div class="footer__header">
                    Ссылки
                </div>
                <ul class="footer__links-list">
                    <li>
                        <a href="/about/">О компании</a>
                    </li>
                    <li>
                        <a href="/delivery/">Доставка</a>
                    </li>
                    <li>
                        <a href="/reviews/">Отзывы</a>
                    </li>
                    <li>
                        <a href="/jobs/">Вакансии</a>
                    </li>
                    <li>
                        <a href="/address/">Как доехать</a>
                    </li>
                </ul>
            </div>
            <div class="footer__links">
                <div class="footer__header">
                    Наши адреса
                </div>
				<ul class="footer__links-list">
                    <li>
						<a data-fancybox data-type="iframe" data-width="900" data-height="450" data-src="https://yandex.ru/map-widget/v1/?um=constructor%3A7a1b74795bbac9367df4b064a39809ff57003c3565e81e4120646df3e4890d6e&amp;source=constructor" href="javascript:;">Фудмаркет Балчуг, ул. Балчуг, 5 </a>
                    </li>
                    <li>
						<a data-fancybox data-type="iframe" data-width="900" data-height="450" data-src="https://yandex.ru/map-widget/v1/?um=constructor%3Ab44598b9a4d58b68616b14f6cbc4e6a82e90c14e612b9da93334397db743420d&amp;source=constructor" href="javascript:;">Фудмаркет Станколит, ул. Складочная, 1 с1 </a>
                    </li>
                    <li>
						<a data-fancybox data-type="iframe" data-width="900" data-height="450" data-src="https://yandex.ru/map-widget/v1/?um=constructor%3A9deb41e0ae002b08ef68f3e40e0df0c52c829a98846d4dd4615f6b2d57f69720&amp;source=constructor" href="javascript:;">Фудхолл Поляна, ул. Складочная, 1 с9 </a>
                    </li>
                    <li>
					<a data-fancybox data-type="iframe" data-width="900" data-height="450" data-src="https://yandex.ru/map-widget/v1/?um=constructor%3Ab202cb0f00824c777d2c88dae93adf6ec42ea611942db81e914810b7fcdf0d00&amp;source=constructor" href="javascript:;">ТРЦ Саларис, Киевское шоссе, 23-й километр  </a>
                    </li>
                    <li>
						<a data-fancybox data-type="iframe" data-width="900" data-height="450" data-src="https://yandex.ru/map-widget/v1/?um=constructor%3A7a2220c40f883a218d6f25d4c91e323fb23f1467598db3fc53355b3d155de0bf&amp;source=constructor" href="javascript:;">Фудхолл Тишинка, Тишинская площадь, 1, стр.1 </a>
                    </li>
                </ul>
                <!-- <ul class="footer__links-list">
                    <li>
                        Ленина, 112, офис 43
                    </li>
                    <li>
                        Ленина, 112, офис 43
                    </li>
                    <li>
                        Дзержинского, 333, офис 33
                    </li>
                    <li>
                        Дзержинского, 333, офис 33
                    </li>
                </ul> -->
            </div>
        </div>
    </div>
    <div class="container border-top">
        <div class="footer__third">
            <div class="credits">© <?php echo date('Y');?>, Название</div>
            <div class="confidential">
                <a href="/confidential/" class="confidential-link">Условия использования</a>
            </div>

            <div class="social">
                <ul>
                    <li>
                        <a href="#">
                            <svg class="icon icon-social">
                                <use xlink:href="#vk"></use>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <svg class="icon icon-social">
                                <use xlink:href="#inst"></use>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <svg class="icon icon-social">
                                <use xlink:href="#fb"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="disclaimer">
            Мы используем cookies для быстрой и удобной работы сайта. Продолжая пользоваться сайтом, вы принимаете условия обработки персональных данных
        </div>
    </div>
</footer>
<div id="modal-login" class="modal">
    <h2>Вход на сайт</h2>
    <div class="login-step-1 login-step">
        <form action="">
            <div class="modal-row">
                <label for="login-phone">Номер телефона</label>
                <div class="modal-row__phone">
                    <input class="text-field mask_phone" type="text" value="+7" name="login-phone">
                </div>
            </div>
            <span class="error"></span>
            <div class="modal-control">
                <a href="#" class="button" id="send-code">
                    <span>Выслать код</span>
                </a>
            </div>
        </form>
    </div>
    <div class="login-step-2 login-step" style="display: none;">
        <form action="">
            <div class="modal-row">
                <label for="login-phone">Код из смс</label>
                <div class="modal-row__code">
                    <input class="text-field" type="text" id="login-code">
                </div>
            </div>
            <span class="error"></span>
            <div class="modal-control">
                <a href="#" class="button" id="login">
                    <span>Продолжить</span>
                </a>
            </div>
        </form>
    </div>
</div>
<div id="modal-city" class="modal modal-city">
    <div class="modal-city__header">Выберите город</div>
    <ul class="modal-city__list">
        <li>
            <a href="#">Ижевск, Удмуртская республика</a>
        </li>
        <li>
            <a href="#">Москва</a>
        </li>
        <li>
            <a href="#">Санкт-Петербург</a>
        </li>
        <li>
            <a href="#">Екатеринбург, Свердловская область</a>
        </li>
        <li>
            <a href="#">Нижний Новгород, Нижегородская область</a>
        </li>
    </ul>
</div>
<div class="yamap" style="position: relative;">
    <div id="<?= TaxiOrder::Config('html.elements.id.map') ?>"
         style="width: 100%; height: 1px; z-index: 2;"></div>
    <div id="error"></div>
</div>
</body>
</html>
	

	<!-- javascript-->
	<script type="text/javascript" src="<?= SITE_TEMPLATE_PATH?>/restosite/slick/slick.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js" type="text/javascript"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js" integrity="sha512-uURl+ZXMBrF4AwGaWmEetzrd+J5/8NRkWAvJx5sbPSSuOb0bZLqf+tOzniObO00BjHa/dD7gub9oCGMLPQHtQA==" crossorigin="anonymous"></script>
        <script src="<?= SITE_TEMPLATE_PATH?>/libs.js"></script>
        <script src="<?=SITE_TEMPLATE_PATH?>/restosite/main.js"></script>

        <script type="text/javascript" src="/include/taxiorder/js/FactoryTaxiOrder.js"></script>
        <script src="<?= SITE_TEMPLATE_PATH?>/scripts.js"></script>
        <?php //require $_SERVER['DOCUMENT_ROOT'] . '/api_integration/include/bitrix/templates/after_footer.php'; ?>
        <?//if (strpos($currentPage, 'cart')):?>
            <script src="/local/templates/restoran/taxiorder/js/template.js"></script>
        <?//endif;?>
		<script src="<?= SITE_TEMPLATE_PATH?>/js/datepicker.min.js"></script>

		<script>
			$('[data-fancybox]').fancybox({
				toolbar  : false,
				smallBtn : true,
				iframe : {
				preload : false
				}
			})
		</script>

        <!-- <script src="//api-maps.yandex.ru/2.1/?load=package.full&lang=ru-RU"></script>
        <script type="text/javascript">
            window.geoservice = 'yandex';
        </script> -->

    </body>
</html>