<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>
<div class="cabinet-tabs">
    <?if (!empty($arResult)):?>
       <?foreach($arResult as $i => $arItem):?>
          <a href="<?=$arItem['LINK']?>" <?if($arItem['SELECTED']):?>class="active<?endif?>"><?=$arItem['TEXT']?></a>
       <?endforeach?>
    <?endif;?>
</div>