<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (!empty($arResult)) {
	$parentID = false;
	$subParentID = false;
	foreach($arResult as $i => $arItem) {
		if ($arItem['DEPTH_LEVEL'] == 1) {
			$parentID = $i;
			$arResult[$i]['ITEMS'] = array();
		} elseif ($arItem['DEPTH_LEVEL']==2 && $parentID!==false) {
			$arResult[$parentID]['ITEMS'][$i] = $arItem;
			$subParentID = $i;
			unset($arResult[$i]);
		} elseif ($arItem['DEPTH_LEVEL']==3 && isset($arResult[$parentID]['ITEMS'][$subParentID])) {
			if (!isset($arResult[$parentID]['ITEMS'][$subParentID]['ITEMS'])) {
				$arResult[$parentID]['ITEMS'][$subParentID]['ITEMS'] = array();
			}
			$arResult[$parentID]['ITEMS'][$subParentID]['ITEMS'][] = $arItem;
			unset($arResult[$i]);
		}
	}
	$arResult = array_values($arResult);
}