<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Товары");
?>
<?
//если категория выбрана
$queries = array();
parse_str($_SERVER['QUERY_STRING'], $queries);
$sect = $queries["CODE"];
if ($sect !== NULL) {
	$arFilter = array(
	"IBLOCK_ID"=>13,
	"SECTION_CODE" => $sect,
	);?>

	<?$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DETAIL_TEXT","PROPERTY_*");
	$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
	$items = [];?>
	<section class="goods">
		<div class="container">
			<h2 class="h2">
				<?
				$el = CIBlockSection::GetList(
					array(), 
					array('IBLOCK_ID'=>13,'=CODE' => $sect), false, array('nTopCount' => 1), 
					array('IBLOCK_ID','ELEMENT_ID','NAME')
				)->Fetch();echo $el["NAME"];
				?>
			</h2>
			<div class="goods__inner">
				<?while($ob = $res->GetNextElement())
				{
				$arFields = $ob->GetFields();

				$item = [
					'Name' => 	$arFields["NAME"],
					'Descr' => 	$arFields["DETAIL_TEXT"],
					'Badge' => 	$arFields["PROPERTY_31"],
					'Id' => 	$arFields["PROPERTY_34"],
					'Img' => 	$arFields["PROPERTY_36"],
					'Types' => 	$arFields["PROPERTY_32"],
					'Dops' => 	$arFields["PROPERTY_33"],
				];

				$items[] = $item;

				$types = [];
				foreach ($item['Types'] as $itemType){
					$types[] = json_decode($itemType["TEXT"], true);
				}

				$dops = [];
				foreach ($item['Dops'] as $itemDop){
					$dops[] = json_decode($itemDop["TEXT"], true);
				}
			
				
				?>
				<div class="goods__item js-item" data-id="<?=$item['Id'];?>">
					<div class="goods__item-img-wrap">
						<div class="badge js-item-badge" data-badge="<?=$item['Badge'];?>">
							<svg class="icon">
								<use xlink:href="#<?=$item['Badge'];?>"></use>
							</svg>
						</div>
						<div class="goods__item-img js-item-img" style="background-image:url(<?=$item['Img'];?>);" data-img="<?=$item['Img'];?>"></div>
					</div>
					<div class="goods__item-text">
						<a href="#modal-card-<?=$item['Id'];?>" rel="modal:open" class="goods__item-name js-item-name">
							<?=$item['Name'];?>
						</a>
						<div class="goods__item-description  js-item-description">
							<?=$item['Descr'];?>
						</div>
						<div class="js-types<?if (count($types)>1):?> has-options<?endif;?>" style="display: none;">
							<input class="styled-radio" type="radio" 
								data-cost="<?=round($types[0]['cost'])?>" 
								data-id="<?=$types[0]['id'];?>" 
								data-name="<?=$types[0]['name'];?>"
								data-description="<?=$types[0]['description'];?>" 
								checked >
						</div>
						<div class="js-dops<?if (count($dops)>0):?> has-options<?endif;?>" style="display: none;">
							<input class="styled-checkbox" type="checkbox" 
								data-cost="<?=round($dops[0]['cost'])?>" 
								data-id="<?=$dops[0]['id'];?>" 
								data-name="<?=$dops[0]['name'];?>"
								data-description="<?=$dops[0]['description'];?>" >
						</div>
						<div class="goods__item-footer">
							<div class="goods__item-price js-item-cost"><span><?=round($types[0]["cost"]);?></span> ₽</div>
							<?if ( count($types)>1 || count($dops)>0):?>
							<div class="goods__item-controls">
								<a href="#modal-card-<?=$item['Id'];?>" rel="modal:open" class="button button--bordered">
									<span>
										Выбрать
									</span>
								</a>
							</div>
							<?else:?>
							<div class="js-control-type" data-type="<?=$types[0]["id"]?>">
								<div class="goods__item-controls js-controls" data-quantity="0">
									<a href="#" class="button js-add">
										<svg class="icon icon-plus">
											<use xlink:href="#plus"></use>
										</svg>
										<span>
											В корзину
										</span>
									</a>
								</div>
								<div class="goods__item-controls js-controls" data-quantity="0" style="display: none;">
									<a href="#" class="goods__item-control js-remove">
		                                <svg class="icon icon-remove">
		                                    <use xlink:href="#remove"></use>
		                                </svg>
		                            </a>
		                            <span class="quantity">1</span>
		                            <a href="#" class="goods__item-control js-add">
		                                <svg class="icon icon-add">
		                                    <use xlink:href="#add"></use>
		                                </svg>
		                            </a>
								</div>
							</div>
							<?endif;?>
						</div>
					</div>
				</div>
				
				<?
				}
				?>
			</div>
		</div>
	</section>

	<?foreach($items as $item):?>
	<div id="modal-card-<?=$item['Id'];?>" class="modal modal-card js-item" data-id="<?=$item['Id'];?>">
	    <div class="modal-card__inner">
	        <div class="modal-card__main">
	            <div class="modal-card__img js-item-img" style="background-image:url(<?=$item['Img'];?>);" data-img="<?=$item['Img'];?>">
	                <div class="badge js-item-badge" data-badge="<?=$item['Badge'];?>">
	                    <svg class="icon">
	                        <use xlink:href="#<?=$item['Badge'];?>"></use>
	                    </svg>
	                </div>
	            </div>
	            <div class="modal-card__text">
	                <div class="modal-card__header js-item-name">
	                    <?=$item['Name'];?>
	                </div>
	                <div class="modal-card__description js-item-description">
	                    <?=$item['Descr'];?>
	                </div>
	                <?
						$types = [];
						foreach ($item['Types'] as $itemType){
							$types[] = json_decode($itemType["TEXT"], true);
						}

						$dops = [];
						foreach ($item['Dops'] as $itemDop){
							$dops[] = json_decode($itemDop["TEXT"], true);
						}
	                ?>
	                <div class="modal-card__settings">
	                <?if (count($types)>1):?>
	                <?$i = 0;?>
	                    <div class="modal-card__settings-header">
	                        ВАРИАНТЫ
	                    </div>
	                    <ul class="modal-card__settings-list js-types">
	                    	<?foreach($types as $type):?>
	                        <li class="modal-card__settings-item">
	                            <div class="modal-card__settings-item-left">
	                                <input class="styled-radio" type="radio" 
		                                id="item-<?=$item['Id'];?>_type-<?=$type['id']?>" 
		                                name="item-<?=$item['Id'];?>_types" 
		                                data-cost="<?=round($type['cost'])?>" 
		                                data-id="<?=$type['id'];?>" 
		                                data-name="<?=$type['name'];?>"
		                                data-description="<?=$type['description'];?>"
	                                <?if($i == 0) echo 'checked'?>/>
	                                <label for="item-<?=$item['Id'];?>_type-<?=$type['id']?>"><span class="control"></span><span class="text">
	                                	<?=$type['name']?> 
	                                	<?if ($type['description'] !== ''):?>
	                                		(<?=$type['description']?>)
	                                	<?endif;?>
	                                	</span>
	                                </label>
	                            </div>
	                            <div class="modal-card__settings-item-price">
	                                <span><?=round($type['cost'])?></span> ₽
	                            </div>
	                        </li>
	                        <?$i++;?>
	                        <?endforeach;?>
	                    </ul>
	                    <?else:?>
	                    <div class="js-types" style="display: none;">
		                    <input class="styled-radio" type="radio" 
		                        id="item-<?=$item['Id'];?>_type-<?=$types[0]['id']?>" 
		                        name="item-<?=$item['Id'];?>_types" 
		                        data-cost="<?=round($types[0]['cost'])?>" 
		                        data-id="<?=$types[0]['id'];?>" 
		                        data-name="<?=$types[0]['name'];?>"
		                        data-description="<?=$types[0]['description'];?>" 
		                        checked/>
		                </div>
	                    <?endif;?>
	                    <?if (count($dops)>0):?>
		                    <div class="modal-card__settings-header">
		                        ПОЖЕЛАНИЯ
		                    </div>
		                    <ul class="modal-card__settings-list js-dops">
		                    	<?foreach($dops as $dop):?>
		                        <li class="modal-card__settings-item">
		                            <div class="modal-card__settings-item-left">
		                                <input class="styled-checkbox" 
		                                	type="checkbox" 
		                                	id="item-<?=$item['Id'];?>_dop-<?=$dop['id']?>" 
		                                	name="item-<?=$item['Id'];?>_dops" 
		                                	data-name="<?=$dop['name']?>"
		                                	data-id="<?=$dop['id']?>"
		                                	data-cost="<?=round($dop['cost'])?>"
		                                	/>
		                                <label for="item-<?=$item['Id'];?>_dop-<?=$dop['id']?>"><span class="control"></span><span class="text"><?=$dop['name']?></span></label>
		                            </div>
		                            <div class="modal-card__settings-item-price">
		                                <span><?=round($dop['cost'])?></span> ₽
		                            </div>
		                        </li>
		                        <?endforeach;?>
		                    </ul>
	                	<?endif;?>
	                </div>
	            </div>
	        </div>
	        <div class="modal-card__footer">
	            <div class="modal-card__price js-item-cost">
		            <span><?=round($types[0]["cost"]);?></span> ₽
		        </div>
		        <?$y=0;?>
		        <?foreach($types as $type):?>
		        <div class="js-control-type" data-type="<?=$type["id"]?>" <?if ($y !== 0) echo "style='display:none;'"?> >
			        <a href="#" class="button js-add js-controls" data-quantity="0">
		                <svg class="icon icon-plus">
		                    <use xlink:href="#plus"></use>
		                </svg>
		                <span>В корзину</span>
		            </a>
		            <div class="js-controls" style="display: none;" data-quantity="0">
		            	<a href="#" class="goods__item-control js-remove">
		                    <svg class="icon icon-remove">
		                        <use xlink:href="#remove"></use>
		                    </svg>
		                </a>
		                <span class="quantity">1</span>
		                <a href="#" class="goods__item-control js-add">
		                    <svg class="icon icon-add">
		                        <use xlink:href="#add"></use>
		                    </svg>
		                </a>
		            </div>
		        </div>
		        <?$y++;?>
		        <?endforeach;?>
	        </div>
	    </div>
	</div>
	<?endforeach;?>

<?
} else {
	$IBLOCK_ID = 13;
 
	$arOrder = Array("SORT"=>"AES");
	$arSelect = Array("ID", "NAME", "SECTION_CODE");
	$arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID, "ACTIVE"=>"Y");
	$res = CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);
				 
	$sects = [];

	while($ob = $res->GetNextElement())
	{
		$arFields = $ob->GetFields();
		 $sects[] = [
		 	'ID' => $arFields["ID"],
		 	'NAME' => $arFields["NAME"],
		 ];
	}

	foreach ($sects as $key => $sect): ?>
		<? $arFilter = array(
			'IBLOCK_ID' =>13,
			'SECTION_ID' => $sect['ID'],
		); 


	$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DETAIL_TEXT","PROPERTY_*");
	$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
	$items = [];?>

	<section class="goods">
		<div class="container">
			<h2 class="h2">
				<?= $sect['NAME']; ?>
			</h2>
			<div class="goods__inner">
				<?while($ob = $res->GetNextElement())
				{
				$arFields = $ob->GetFields();

				$item = [
					'Name' => 	$arFields["NAME"],
					'Descr' => 	$arFields["DETAIL_TEXT"],
					'Badge' => 	$arFields["PROPERTY_31"],
					'Id' => 	$arFields["PROPERTY_34"],
					'Img' => 	$arFields["PROPERTY_36"],
					'Types' => 	$arFields["PROPERTY_32"],
					'Dops' => 	$arFields["PROPERTY_33"],
				];

				$items[] = $item;

				$types = [];
				foreach ($item['Types'] as $itemType){
					$types[] = json_decode($itemType["TEXT"], true);
				}

				$dops = [];
				foreach ($item['Dops'] as $itemDop){
					$dops[] = json_decode($itemDop["TEXT"], true);
				}		
				
				?>
				<div class="goods__item js-item" data-id="<?=$item['Id'];?>">
					<div class="goods__item-img-wrap">
						<div class="badge js-item-badge" data-badge="<?=$item['Badge'];?>">
							<svg class="icon">
								<use xlink:href="#<?=$item['Badge'];?>"></use>
							</svg>
						</div>
						<div class="goods__item-img js-item-img" style="background-image:url(<?=$item['Img'];?>);" data-img="<?=$item['Img'];?>"></div>
					</div>
					<div class="goods__item-text">
						<a href="#modal-card-<?=$item['Id'];?>" rel="modal:open" class="goods__item-name js-item-name">
							<?=$item['Name'];?>
						</a>
						<div class="goods__item-description  js-item-description">
							<?=$item['Descr'];?>
						</div>
						<div class="js-types<?if (count($types)>1):?> has-options<?endif;?>" style="display: none;">
							<input class="styled-radio" type="radio" 
								data-cost="<?=round($types[0]['cost'])?>" 
								data-id="<?=$types[0]['id'];?>" 
								data-name="<?=$types[0]['name'];?>"
								data-description="<?=$types[0]['description'];?>" 
								checked >
						</div>
						<div class="js-dops<?if (count($dops)>1):?> has-options<?endif;?>" style="display: none;">
							<input class="styled-checkbox" type="checkbox" 
								data-cost="<?=round($dops[0]['cost'])?>" 
								data-id="<?=$dops[0]['id'];?>" 
								data-name="<?=$dops[0]['name'];?>"
								data-description="<?=$dops[0]['description'];?>" >
						</div>
						<div class="goods__item-footer">
							<div class="goods__item-price js-item-cost"><span><?=round($types[0]["cost"]);?></span> ₽</div>
							<?if ( count($types)>1 || count($dops)>0):?>
							<div class="goods__item-controls">
								<a href="#modal-card-<?=$item['Id'];?>" rel="modal:open" class="button button--bordered">
									<span>
										Выбрать
									</span>
								</a>
							</div>
							<?else:?>
							<div class="js-control-type" data-type="<?=$types[0]["id"]?>">
								<div class="goods__item-controls js-controls" data-quantity="0">
									<a href="#" class="button js-add">
										<svg class="icon icon-plus">
											<use xlink:href="#plus"></use>
										</svg>
										<span>
											В корзину
										</span>
									</a>
								</div>
								<div class="goods__item-controls js-controls" data-quantity="0" style="display: none;">
									<a href="#" class="goods__item-control js-remove">
		                                <svg class="icon icon-remove">
		                                    <use xlink:href="#remove"></use>
		                                </svg>
		                            </a>
		                            <span class="quantity">1</span>
		                            <a href="#" class="goods__item-control js-add">
		                                <svg class="icon icon-add">
		                                    <use xlink:href="#add"></use>
		                                </svg>
		                            </a>
								</div>
							</div>
							<?endif;?>
						</div>
					</div>
				</div>
				
				<?
				}
				?>
			</div>
		</div>
	</section>

	<?foreach($items as $item):?>
	<div id="modal-card-<?=$item['Id'];?>" class="modal modal-card js-item" data-id="<?=$item['Id'];?>">
	    <div class="modal-card__inner">
	        <div class="modal-card__main">
	            <div class="modal-card__img js-item-img" style="background-image:url(<?=$item['Img'];?>);" data-img="<?=$item['Img'];?>">
	                <div class="badge js-item-badge" data-badge="<?=$item['Badge'];?>">
	                    <svg class="icon">
	                        <use xlink:href="#<?=$item['Badge'];?>"></use>
	                    </svg>
	                </div>
	            </div>
	            <div class="modal-card__text">
	                <div class="modal-card__header js-item-name">
	                    <?=$item['Name'];?>
	                </div>
	                <div class="modal-card__description js-item-description">
	                    <?=$item['Descr'];?>
	                </div>
	                <?
						$types = [];
						foreach ($item['Types'] as $itemType){
							$types[] = json_decode($itemType["TEXT"], true);
						}

						$dops = [];
						foreach ($item['Dops'] as $itemDop){
							$dops[] = json_decode($itemDop["TEXT"], true);
						}
	                ?>
	                <div class="modal-card__settings">
	                <?if (count($types)>1):?>
	                <?$i = 0;?>
	                    <div class="modal-card__settings-header">
	                        ВАРИАНТЫ
	                    </div>
	                    <ul class="modal-card__settings-list js-types">
	                    	<?foreach($types as $type):?>
	                        <li class="modal-card__settings-item">
	                            <div class="modal-card__settings-item-left">
	                                <input class="styled-radio" type="radio" 
		                                id="item-<?=$item['Id'];?>_type-<?=$type['id']?>" 
		                                name="item-<?=$item['Id'];?>_types" 
		                                data-cost="<?=round($type['cost'])?>" 
		                                data-id="<?=$type['id'];?>" 
		                                data-name="<?=$type['name'];?>"
		                                data-description="<?=$type['description'];?>"
	                                <?if($i == 0) echo 'checked'?>/>
	                                <label for="item-<?=$item['Id'];?>_type-<?=$type['id']?>"><span class="control"></span><span class="text">
	                                	<?=$type['name']?> 
	                                	<?if ($type['description'] !== ''):?>
	                                		(<?=$type['description']?>)
	                                	<?endif;?>
	                                	</span>
	                                </label>
	                            </div>
	                            <div class="modal-card__settings-item-price">
	                                <span><?=round($type['cost'])?></span> ₽
	                            </div>
	                        </li>
	                        <?$i++;?>
	                        <?endforeach;?>
	                    </ul>
	                    <?else:?>
	                    <div class="js-types" style="display: none;">
		                    <input class="styled-radio" type="radio" 
		                        id="item-<?=$item['Id'];?>_type-<?=$types[0]['id']?>" 
		                        name="item-<?=$item['Id'];?>_types" 
		                        data-cost="<?=round($types[0]['cost'])?>" 
		                        data-id="<?=$types[0]['id'];?>" 
		                        data-name="<?=$types[0]['name'];?>"
		                        data-description="<?=$types[0]['description'];?>" 
		                        checked/>
		                </div>
	                    <?endif;?>
	                    <?if (count($dops)>1):?>
		                    <div class="modal-card__settings-header">
		                        ПОЖЕЛАНИЯ
		                    </div>
		                    <ul class="modal-card__settings-list js-dops">
		                    	<?foreach($dops as $dop):?>
		                        <li class="modal-card__settings-item">
		                            <div class="modal-card__settings-item-left">
		                                <input class="styled-checkbox" 
		                                	type="checkbox" 
		                                	id="item-<?=$item['Id'];?>_dop-<?=$dop['id']?>" 
		                                	name="item-<?=$item['Id'];?>_dops" 
		                                	data-name="<?=$dop['name']?>"
		                                	data-id="<?=$dop['id']?>"
		                                	data-cost="<?=round($dop['cost'])?>"
		                                	/>
		                                <label for="item-<?=$item['Id'];?>_dop-<?=$dop['id']?>"><span class="control"></span><span class="text"><?=$dop['name']?></span></label>
		                            </div>
		                            <div class="modal-card__settings-item-price">
		                                <span><?=round($dop['cost'])?></span> ₽
		                            </div>
		                        </li>
		                        <?endforeach;?>
		                    </ul>
	                	<?endif;?>
	                </div>
	            </div>
	        </div>
	        <div class="modal-card__footer">
	            <div class="modal-card__price js-item-cost">
		            <span><?=round($types[0]["cost"]);?></span> ₽
		        </div>
		        <?$y=0;?>
		        <?foreach($types as $type):?>
		        <div class="js-control-type" data-type="<?=$type["id"]?>" <?if ($y !== 0) echo "style='display:none;'"?> >
			        <a href="#" class="button js-add js-controls" data-quantity="0">
		                <svg class="icon icon-plus">
		                    <use xlink:href="#plus"></use>
		                </svg>
		                <span>В корзину</span>
		            </a>
		            <div class="js-controls" style="display: none;" data-quantity="0">
		            	<a href="#" class="goods__item-control js-remove">
		                    <svg class="icon icon-remove">
		                        <use xlink:href="#remove"></use>
		                    </svg>
		                </a>
		                <span class="quantity">1</span>
		                <a href="#" class="goods__item-control js-add">
		                    <svg class="icon icon-add">
		                        <use xlink:href="#add"></use>
		                    </svg>
		                </a>
		            </div>
		        </div>
		        <?$y++;?>
		        <?endforeach;?>
	        </div>
	    </div>
	</div>
	<? endforeach; ?>

<? endforeach; ?>

<?
}
?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>