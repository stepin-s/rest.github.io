<?
if (!defined("SEO_ELEMENT") || (true!=constant("SEO_ELEMENT")))
    return;

$APPLICATION->SetTitle($SEO_ELEMENT["PAGE_TITLE"]);
$APPLICATION->SetPageProperty("title", $SEO_ELEMENT["BROWSER_TITLE"]);
$APPLICATION->SetPageProperty("keywords", $SEO_ELEMENT["KEYWORDS"]);
$APPLICATION->SetPageProperty("description", $SEO_ELEMENT["DESCRIPTION"]);
?>