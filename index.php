<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetTitle('Главная');
setcookie('CITY_LAT', '56.852774927252895', time() + 10000, '/');//ижевск
setcookie('CITY_LON', '53.21146331000002', time() + 10000, '/');
$cart = GGootaxbase::getCart();
?>

<? $APPLICATION->IncludeComponent("bitrix:news.list", "slider", [
    "ACTIVE_DATE_FORMAT" => "d.m.Y",    // Формат показа даты
    "ADD_SECTIONS_CHAIN" => "N",    // Включать раздел в цепочку навигации
    "AJAX_MODE" => "N",    // Включить режим AJAX
    "AJAX_OPTION_ADDITIONAL" => "",    // Дополнительный идентификатор
    "AJAX_OPTION_HISTORY" => "N",    // Включить эмуляцию навигации браузера
    "AJAX_OPTION_JUMP" => "N",    // Включить прокрутку к началу компонента
    "AJAX_OPTION_STYLE" => "Y",    // Включить подгрузку стилей
    "CACHE_FILTER" => "N",    // Кешировать при установленном фильтре
    "CACHE_GROUPS" => "Y",    // Учитывать права доступа
    "CACHE_TIME" => "36000000",    // Время кеширования (сек.)
    "CACHE_TYPE" => "A",    // Тип кеширования
    "CHECK_DATES" => "Y",    // Показывать только активные на данный момент элементы
    "DETAIL_URL" => "",    // URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
    "DISPLAY_BOTTOM_PAGER" => "Y",    // Выводить под списком
    "DISPLAY_DATE" => "N",    // Выводить дату элемента
    "DISPLAY_NAME" => "Y",    // Выводить название элемента
    "DISPLAY_PICTURE" => "Y",    // Выводить изображение для анонса
    "DISPLAY_PREVIEW_TEXT" => "N",    // Выводить текст анонса
    "DISPLAY_TOP_PAGER" => "N",    // Выводить над списком
    "FIELD_CODE" => [    // Поля
                         0 => "NAME",
                         1 => "PREVIEW_PICTURE",
                         2 => "",
    ],
    "FILTER_NAME" => "",    // Фильтр
    "HIDE_LINK_WHEN_NO_DETAIL" => "N",    // Скрывать ссылку, если нет детального описания
    "IBLOCK_ID" => "14",    // Код информационного блока
    "IBLOCK_TYPE" => "slider",    // Тип информационного блока (используется только для проверки)
    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",    // Включать инфоблок в цепочку навигации
    "INCLUDE_SUBSECTIONS" => "Y",    // Показывать элементы подразделов раздела
    "MESSAGE_404" => "",    // Сообщение для показа (по умолчанию из компонента)
    "NEWS_COUNT" => "20",    // Количество новостей на странице
    "PAGER_BASE_LINK_ENABLE" => "N",    // Включить обработку ссылок
    "PAGER_DESC_NUMBERING" => "N",    // Использовать обратную навигацию
    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",    // Время кеширования страниц для обратной навигации
    "PAGER_SHOW_ALL" => "N",    // Показывать ссылку "Все"
    "PAGER_SHOW_ALWAYS" => "N",    // Выводить всегда
    "PAGER_TEMPLATE" => ".default",    // Шаблон постраничной навигации
    "PAGER_TITLE" => "Новости",    // Название категорий
    "PARENT_SECTION" => "",    // ID раздела
    "PARENT_SECTION_CODE" => "",    // Код раздела
    "PREVIEW_TRUNCATE_LEN" => "",    // Максимальная длина анонса для вывода (только для типа текст)
    "PROPERTY_CODE" => [    // Свойства
                            0 => "LINK",
                            1 => "",
    ],
    "SET_BROWSER_TITLE" => "N",    // Устанавливать заголовок окна браузера
    "SET_LAST_MODIFIED" => "N",    // Устанавливать в заголовках ответа время модификации страницы
    "SET_META_DESCRIPTION" => "N",    // Устанавливать описание страницы
    "SET_META_KEYWORDS" => "N",    // Устанавливать ключевые слова страницы
    "SET_STATUS_404" => "N",    // Устанавливать статус 404
    "SET_TITLE" => "N",    // Устанавливать заголовок страницы
    "SHOW_404" => "N",    // Показ специальной страницы
    "SORT_BY1" => "ACTIVE_FROM",    // Поле для первой сортировки новостей
    "SORT_BY2" => "SORT",    // Поле для второй сортировки новостей
    "SORT_ORDER1" => "DESC",    // Направление для первой сортировки новостей
    "SORT_ORDER2" => "ASC",    // Направление для второй сортировки новостей
    "STRICT_SECTION_CHECK" => "N",    // Строгая проверка раздела для показа списка
],
    false
); ?>

<?

$IBLOCK_ID = 13;

$arOrder = ["SORT" => "AES"];
$arSelect = ["ID", "NAME", "SECTION_CODE"];
$arFilter = ["IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y"];
$res = CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);

$sects = [];

while ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields();
    $sects[] = [
        'ID' => $arFields["ID"],
        'NAME' => $arFields["NAME"],
    ];
}
$appendedId = [];
foreach ($sects as $key => $sect): ?>
    <? $arFilter = [
        'IBLOCK_ID' => 13,
        'SECTION_ID' => $sect['ID'],
    ];

    if ($key === 1) {

        $APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"banners_main", 
	array(
		"IBLOCK_ID" => "20",
		"PROPERTY_CODE" => array(
			0 => "LINK",
			1 => "",
		),
		"NEWS_COUNT" => "1",
		"COMPONENT_TEMPLATE" => "banners_main",
		"IBLOCK_TYPE" => "news",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_LAST_MODIFIED" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"STRICT_SECTION_CHECK" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SET_STATUS_404" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => ""
	),
	false
);
    }

    $arSelect = ["ID", "IBLOCK_ID", "NAME", "DETAIL_TEXT", "PROPERTY_*"];
    $res = CIBlockElement::GetList([], $arFilter, false, ["nPageSize" => 50], $arSelect);
    $items = []; ?>

	<section class="goods">
		<div class="container">
			<h2 class="h2 c_title" id="section<?= $sect['ID'] ?>">
                <?= $sect['NAME']; ?>
			</h2>
			<div class="goods__inner">
                <? while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();



                    $item = [
                        'Name' => $arFields["NAME"],
                        'Descr' => $arFields["DETAIL_TEXT"],
                        'Badge' => $arFields["PROPERTY_31"],
                        'Id' => $arFields["PROPERTY_34"],
                        'Img' => $arFields["PROPERTY_36"],
                        'Types' => $arFields["PROPERTY_32"],
                        'Dops' => $arFields["PROPERTY_33"],
                        'TYPE_PRICE' => json_decode($arFields["PROPERTY_32"][0]['TEXT'])->description
                    ];

                    $items[] = $item;

                    $types = [];
                    foreach ($item['Types'] as $itemType) {
                        $types[] = json_decode($itemType["TEXT"], true);
                    }

                    $dops = [];
                    foreach ($item['Dops'] as $itemDop) {
                        $dops[] = json_decode($itemDop["TEXT"], true);
                    }

                    ?>
					<div class="goods__item js-item" data-id="<?= $item['Id']; ?>">
						<div class="goods__item-img-wrap">
							<div class=" badge js-item-badge " data-badge="<?= $item['Badge']; ?>">
								<svg class="icon small-badge ">
									<use xlink:href="#<?= $item['Badge']; ?>"></use>
								</svg>
							</div>
							<div class="goods__item-img js-item-img" style="background-image:url(<?= $item['Img']; ?>);" data-img="<?= $item['Img']; ?>"></div>
						</div>
						<div class="goods__item-text">
							<a href="#modal-card-<?= $item['Id']; ?>" rel="modal:open" class="goods__item-name js-item-name">
                                <?= $item['Name']; ?>
							</a>
							<div class="goods__item-description  js-item-description">
								<?= $item['Descr']; ?>
								<div id="c_gram_custom" class="modal-card__price js-item-cost text--grey fs-14">
									<span><?= $item['TYPE_PRICE'] ?></span>
								</div>
							</div>
							<div class="js-types<?
                            if (count($types) > 1):?> has-options<?endif; ?>" style="display: none;">
								<input class="styled-radio" type="radio"
								       data-cost="<?= round($types[0]['cost']) ?>"
								       data-id="<?= $types[0]['id']; ?>"
								       data-name="<?= $types[0]['name']; ?>"
								       data-description="<?= $types[0]['description']; ?>"
								       checked>
							</div>
							<div class="js-dops<?
                            if (count($dops) > 0):?> has-options<?endif; ?>" style="display: none;">
								<input class="styled-checkbox" type="checkbox"
								       data-cost="<?= round($dops[0]['cost']) ?>"
								       data-id="<?= $dops[0]['id']; ?>"
								       data-name="<?= $dops[0]['name']; ?>"
								       data-description="<?= $dops[0]['description']; ?>">
							</div>
							<div class="goods__item-footer">
								<div class="goods__item-price js-item-cost c_price fs-16">
									<span><?= round($types[0]["cost"]); ?></span> ₽
								</div>
								<div id="c_gram_regular" class="modal-card__price js-item-cost text--grey fs-14">
									<span><?= $item['TYPE_PRICE'] ?></span>
								</div>
                                <?
                                if ((count($types) > 1 /*|| count($dops) > 0*/) && isset($cart[$item['Id']]) ):?>
									<div class="goods__item-controls">
										<a href="#modal-card-<?= $item['Id']; ?>" rel="modal:open" class="button button--bordered" id="sm-n">
									<span>
										Выбрать
									</span>
										</a>
										<a href="#modal-card-<?= $item['Id']; ?>" rel="modal:open"   class="button " id="sm-y" style="display:none;">
											<svg class="icon icon-plus">
												<use xlink:href="#plus"></use>
											</svg>
											<svg class="icon icon-cart-white-fill" width="28" height="22"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 22" id="cart-white-fill"
											style="display:none;float: right; margin-top: 2%;">
   												 <path d="M19.29 0l5.147 6.34H28L24.365 22H3.635L0 6.34h3.275L8.422 0l1.691 1.331-4.103 5.01h15.656l-4.067-5.01L19.291 0z" fill="#fff"/>
											</svg>
										</a>
									</div>
                                <? elseif(count($types) == 1):?>
								<div class="js-control-type" data-id="<?= $item['Id']; ?>" data-type="<?=$types[0]['id'];?>">
									<div class="goods__item-controls js-controls" data-quantity="0"  <?=in_array($types[0]['id'],$cart) ? 'style="display: none;"' : 'style="display: block;"';?>>
										<a href="javascript:void(0)" rel=""  class="button js-add">
											<svg class="icon icon-plus">
												<use xlink:href="#plus"></use>
											</svg>
											<svg class="icon icon-cart-white-fill" width="28" height="22"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 22" id="cart-white-fill"
											style="display:none;float: right; margin-top: 2%;">
   												 <path d="M19.29 0l5.147 6.34H28L24.365 22H3.635L0 6.34h3.275L8.422 0l1.691 1.331-4.103 5.01h15.656l-4.067-5.01L19.291 0z" fill="#fff"/>
											</svg>
											<span class="span-add-item">	В корзину</span>
										</a>
									</div>
									<div class="goods__item-controls js-controls quan" data-quantity="0" <?=in_array($types[0]['id'],$cart) ? 'style="display: block;"' : 'style="display: none;"';?>>
										<a href="javascript:void(0)" class="goods__item-control js-remove">
											<svg class="icon icon-remove">
												<use xlink:href="#remove"></use>
											</svg>
										</a>
										<span class="quantity">1</span>
										<a href="javascript:void(0)" class="goods__item-control js-add">
											<svg class="icon icon-add">
												<use xlink:href="#add"></use>
											</svg>
										</a>
									</div>
								</div>
                                <? else:?>
									<div class="js-control-type" data-id="<?= $item['Id']; ?>" data-type="<?=$types[0]['id'];?>">
										<div class="goods__item-controls js-controls " data-quantity="0" >
										<a href="#modal-card-<?= $item['Id']; ?>" rel="modal:open" class="button button--bordered" id="sm-n">
									<span>
										Выбрать
									</span>
										</a>
										<a href="#modal-card-<?= $item['Id']; ?>" rel="modal:open"   class="button " id="sm-y" style="display:none;">
											<svg class="icon icon-plus">
												<use xlink:href="#plus"></use>
											</svg>
											<svg class="icon icon-cart-white-fill" width="28" height="22"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 22" id="cart-white-fill"
											style="display:none; float: right; margin-top: 2%;">
   												 <path d="M19.29 0l5.147 6.34H28L24.365 22H3.635L0 6.34h3.275L8.422 0l1.691 1.331-4.103 5.01h15.656l-4.067-5.01L19.291 0z" fill="#fff"/>
											</svg>
										</a>
										</div>
<!--										<div class="goods__item-controls js-controls" data-quantity="0"  --><?//=in_array($types[0]['id'],$cart) ? 'style="display: none;"' : '';?><!-->
<!--											<a href="#modal-card---><?//= $item['Id']; ?><!--" rel="modal:open"  class="button ">-->
<!--												<svg class="icon icon-plus">-->
<!--													<use xlink:href="#plus"></use>-->
<!--												</svg>-->
<!--												<span>-->
<!--											В корзину-->
<!--										</span>-->
<!--											</a>-->
<!--										</div>-->

										<?if (count($types) > 1):?>
											<div class="goods__item-controls js-controls" data-quantity="0" style="display: none;">
											<a href="#modal-card-<?= $item['Id']; ?>" rel="modal:open" class="button button--bordered" id="sm-n">
									<span>
										Выбрать
									</span>
										</a>
										<a href="#modal-card-<?= $item['Id']; ?>" rel="modal:open"   class="button " id="sm-y" style="display:none;">
											<svg class="icon icon-plus">
												<use xlink:href="#plus"></use>
											</svg>
											<svg class="icon icon-cart-white-fill" width="28" height="22"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 22" id="cart-white-fill"
											style="display:none; float: right; margin-top: 2%;">
   												 <path d="M19.29 0l5.147 6.34H28L24.365 22H3.635L0 6.34h3.275L8.422 0l1.691 1.331-4.103 5.01h15.656l-4.067-5.01L19.291 0z" fill="#fff"/>
											</svg>
										</a>
											</div>
		                                <?else:?>
											<div class="goods__item-controls js-controls quan" data-quantity="0" <?=in_array($types[0]['id'],$cart) ? 'style="display: block;"' : 'style="display: none;"';?>>
												<a href="javascript:void(0)" class="goods__item-control js-remove">
													<svg class="icon icon-remove">
														<use xlink:href="#remove"></use>
													</svg>
												</a>
												<span class="quantity">1</span>
												<a href="javascript:void(0)" class="goods__item-control js-add">
													<svg class="icon icon-add">
														<use xlink:href="#add"></use>
													</svg>
												</a>
											</div>
		                                <?endif;?>

									</div>
                                <?endif; ?>
							</div>
						</div>
					</div>

                    <?
                }
                ?>
			</div>
		</div>
	</section>

    <?

	foreach ($items as $item):
		if(in_array($item['Id'], $appendedId))
			continue;
	?>
		<div id="modal-card-<?= $item['Id']; ?>" class="modal modal-card js-item" data-id="<?= $item['Id']; ?>">
			<d	v class="modal-card__inner">
				<div class="modal-card__main">
					<div class="modal-card__header js-item-name name-hidden" style="display: none;">
                            <?= $item['Name']; ?>
					</div>
					<div class="modal-card__img js-item-img" style="background-image:url(<?= $item['Img']; ?>);" data-img="<?= $item['Img']; ?>">
						<div class="badge js-item-badge" data-badge="<?= $item['Badge']; ?>">
							<svg class="icon normal-badge ">
								<use xlink:href="#<?= $item['Badge']; ?>"></use>
							</svg>
						</div>
					</div>
					<div class="modal-card__text">
						<div class="modal-card__header js-item-name name-visible">
                            <?= $item['Name']; ?>
						</div>
						<div class="modal-card__description js-item-description">
                            <?= $item['Descr']; ?>
						</div>
                        <?
                        $types = [];
                        foreach ($item['Types'] as $itemType) {
                            $types[] = json_decode($itemType["TEXT"], true);
                        }

                        $dops = [];
                        foreach ($item['Dops'] as $itemDop) {
                            $dops[] = json_decode($itemDop["TEXT"], true);
                        }
                        ?>
						<div class="modal-card__settings">
                            <? if (count($types) > 1): ?>
                                <? $i = 0; ?>
								<div class="modal-card__settings-header">
									ВАРИАНТЫ
								</div>
								<ul class="modal-card__settings-list js-types">
                                    <? foreach ($types as $type): ?>
										<li class="modal-card__settings-item">
											<div class="modal-card__settings-item-left">
												<input class="styled-radio" type="radio"
												       id="item-<?= $item['Id']; ?>_type-<?= $type['id'] ?>"
												       name="item-<?= $item['Id']; ?>_types"
												       data-cost="<?= round($type['cost']) ?>"
												       data-id="<?= $type['id']; ?>"
												       data-name="<?= $type['name']; ?>"
												       data-description="<?= $type['description']; ?>"
                                                    <? if ($i == 0) echo 'checked' ?>/>
												<label for="item-<?= $item['Id']; ?>_type-<?= $type['id'] ?>"><span class="control"></span><span class="text">
	                                	<?= $type['name'] ?>
                                                        <? if ($type['description'] !== ''): ?>
															(<?= $type['description'] ?>)
                                                        <? endif; ?>
	                                	</span>
												</label>
											</div>
											<div class="modal-card__settings-item-price">
												<span><?= round($type['cost']) ?></span> ₽
											</div>
										</li>
                                        <? $i++; ?>
                                    <? endforeach; ?>
								</ul>
                            <? else: ?>
								<div class="js-types" style="display: none;">
									<input class="styled-radio" type="radio"
									       id="item-<?= $item['Id']; ?>_type-<?= $types[0]['id'] ?>"
									       name="item-<?= $item['Id']; ?>_types"
									       data-cost="<?= round($types[0]['cost']) ?>"
									       data-id="<?= $types[0]['id']; ?>"
									       data-name="<?= $types[0]['name']; ?>"
									       data-description="<?= $types[0]['description']; ?>"
									       checked/>
								</div>
                            <? endif; ?>
                            <? if (count($dops) > 0): ?>
								<div class="modal-card__settings-header">
									ПОЖЕЛАНИЯ
								</div>
								<ul class="modal-card__settings-list js-dops">
                                    <? foreach ($dops as $dop): ?>
										<li class="modal-card__settings-item">
											<div class="modal-card__settings-item-left">
												<input class="styled-checkbox"
												       type="checkbox"
												       id="item-<?= $item['Id']; ?>_dop-<?= $dop['id'] ?>"
												       name="item-<?= $item['Id']; ?>_dops"
												       data-name="<?= $dop['name'] ?>"
												       data-id="<?= $dop['id'] ?>"
												       data-cost="<?= round($dop['cost']) ?>"
												/>
												<label for="item-<?= $item['Id']; ?>_dop-<?= $dop['id'] ?>"><span class="control"></span><span class="text"><?= $dop['name'] ?></span></label>
											</div>
											<div class="modal-card__settings-item-price">
												<span><?= round($dop['cost']) ?></span> ₽
											</div>
										</li>
                                    <? endforeach; ?>
								</ul>
                            <? endif; ?>
						</div>
					</div>
				</div>
				<div class="modal-card__footer">
					<div class="modal-card__price js-item-cost c_price">
						<span><?= round($types[0]["cost"]); ?></span> ₽
					</div>
                    <div class="modal-card__price js-item-cost text--grey">
                        <span><?= $item['TYPE_PRICE'] ?></span>
                    </div>
                    <? $y = 0; ?>
                    <? foreach ($types as $type): ?>
						<div class="js-control-type min-max" data-type="<?= $type["id"] ?>" <? if ($y !== 0) echo "style='display:none;'" ?> >
							<a href="javascript:void(0)" class="button js-add js-controls button-l" data-quantity="0">
								<svg class="icon icon-plus icon-plus-l icon-plus-main">
									<use xlink:href="#plus"></use>
								</svg>
								<svg class="icon icon-cart-white-fill icon-cart-white-l" width="28" height="22"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 22" id="cart-white-fill"
											style="display:none;">
   												 <path d="M19.29 0l5.147 6.34H28L24.365 22H3.635L0 6.34h3.275L8.422 0l1.691 1.331-4.103 5.01h15.656l-4.067-5.01L19.291 0z" fill="#fff"/>
											</svg>
								<!-- <svg class="icon icon-cart">
                        			<use xlink:href="#cart"></use>
                    			</svg> -->
								<span class="dn">В корзину</span>
							</a>
							<div class="js-controls min-h-l" style="display: none;" data-quantity="0">
								<a href="javascript:void(0)" class="goods__item-control js-remove">
									<svg class="icon icon-remove">
										<use xlink:href="#remove"></use>
									</svg>
								</a>
								<span class="quantity">1</span>
								<a href="javascript:void(0)" class="goods__item-control js-add">
									<svg class="icon icon-add">
										<use xlink:href="#add"></use>
									</svg>
								</a>
							</div>
						</div>
                        <? $y++; ?>
                    <? endforeach; ?>
				</div>
			</div>
		</div>
    <?
        $appendedId[] = $item['Id'];
		endforeach;
	?>

<? endforeach; ?>

<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>