<?
$name_link = get_uri_path(CUriModule::get_seo_uri());

CModule::includeModule("iblock");
$ar_elements = array();
$ar_filter = array("IBLOCK_ID" => 7, "ACTIVE" => "Y", "NAME" => $name_link);
$ar_select = array(
    "ID",
    "NAME",
    "PROPERTY_BROWSER_TITLE",
    "PROPERTY_PAGE_TITLE",
    "PROPERTY_KEYWORDS",
    "PROPERTY_DESCRIPTION",
    "PROPERTY_SEO_TEXT"
);
$rs_element = CIBlockElement::GetList(array(), $ar_filter, false, false, $ar_select);
if ($ob_element = $rs_element->fetch()) {
    $SEO_ELEMENT["BROWSER_TITLE"] = $ob_element["PROPERTY_BROWSER_TITLE_VALUE"];
    $SEO_ELEMENT["PAGE_TITLE"] = $ob_element["PROPERTY_PAGE_TITLE_VALUE"];
    $SEO_ELEMENT["KEYWORDS"] = $ob_element["PROPERTY_KEYWORDS_VALUE"];
    $SEO_ELEMENT["DESCRIPTION"] = $ob_element["PROPERTY_DESCRIPTION_VALUE"];
    $SEO_ELEMENT["SEO_TEXT"] = $ob_element["PROPERTY_SEO_TEXT_VALUE"]["TEXT"];
    define("SEO_ELEMENT", true);
}
?>