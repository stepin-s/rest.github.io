<div id="f_reviews">
    <?
    $APPLICATION->IncludeComponent(
	"bitrix:iblock.element.add.form", 
	"reviews", 
	array(
		"IBLOCK_TYPE" => "forms",
		"IBLOCK_ID" => "25",
		"STATUS_NEW" => "NEW",
		"LIST_URL" => "",
		"USE_CAPTCHA" => "N",
		"USER_MESSAGE_EDIT" => tr('form_reviews_success'),
		"USER_MESSAGE_ADD" => tr('form_reviews_success'),
		"DEFAULT_INPUT_SIZE" => "30",
		"RESIZE_IMAGES" => "N",
		"PROPERTY_CODES" => array(
			0 => "29",
			1 => "30",
			2 => "31",
			3 => "32",
            4 => "33",
			5 => "NAME",
		),
		"PROPERTY_CODES_REQUIRED" => array(
			0 => "29",
			1 => "30",
			2 => "31",
			3 => "32",
			4 => "NAME",
		),
		"GROUPS" => array(
			0 => "2",
		),
		"STATUS" => "ANY",
		"ELEMENT_ASSOC" => "CREATED_BY",
		"MAX_USER_ENTRIES" => "100000",
		"MAX_LEVELS" => "100000",
		"LEVEL_LAST" => "Y",
		"MAX_FILE_SIZE" => "0",
		"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
		"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
		"SEF_MODE" => "N",
		"SEF_FOLDER" => SITE_DIR."reviews/",
		"CUSTOM_TITLE_NAME" => GetMessage("COLORS3_TAXI_FIO"),
		"CUSTOM_TITLE_TAGS" => "",
		"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
		"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
		"CUSTOM_TITLE_IBLOCK_SECTION" => "",
		"CUSTOM_TITLE_PREVIEW_TEXT" => GetMessage("COLORS3_TAXI_OTZYV"),
		"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
		"CUSTOM_TITLE_DETAIL_TEXT" => "",
		"CUSTOM_TITLE_DETAIL_PICTURE" => "",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"COMPONENT_TEMPLATE" => "reviews"
	),
	false
);
    ?>
</div>