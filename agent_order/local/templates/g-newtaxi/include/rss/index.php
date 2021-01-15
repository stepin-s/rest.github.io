<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?$iblock_id_news = CUriLang::get_instance()->get_iblock_id("news");?>
<?$APPLICATION->IncludeComponent(
	"bitrix:rss.out",
	"",
	Array(
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => $iblock_id_news,
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"NUM_NEWS" => "20",
		"NUM_DAYS" => "30",
		"SORT_BY1" => "",
		"SORT_ORDER1" => "",
		"SORT_BY2" => "",
		"SORT_ORDER2" => "",
		"FILTER_NAME" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"RSS_TTL" => "60",
		"YANDEX" => "Y"
	)
);?>