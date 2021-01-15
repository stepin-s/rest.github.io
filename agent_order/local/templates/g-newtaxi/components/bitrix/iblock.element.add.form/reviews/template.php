<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<?$this->setFrameMode(true);?>
<?$arHidden = array("NAME" => true, "32" => true, "33" => true);?>
<div class="different-information-item">

	<h2>
	    <span class="sprite checkers-icon"></span>
	    Добавить отзыв
	</h2>
<?$bxajaxid = CAjax::GetComponentID($component->__name, $templateName);?>
<div id="<?='ajax_form_' . $bxajaxid?>">
<span class="result_message">
	<?if (count($arResult["ERRORS"])):?>
		<?=ShowError(implode("<br />", $arResult["ERRORS"]))?>
	<?endif?>
	<?if (strlen($arResult["MESSAGE"]) > 0):?>
		<?=ShowNote($arResult["MESSAGE"])?>
	<?endif?>
</span>
		<form class="message-form ajax_form" name="iblock_add" action="<?= POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">

			<?=bitrix_sessid_post()?>

			<?if ($arParams["MAX_FILE_SIZE"] > 0):?><input type="hidden" name="MAX_FILE_SIZE" value="<?=$arParams["MAX_FILE_SIZE"]?>" /><?endif?>


				<?if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):?>


					<?foreach ($arResult["PROPERTY_LIST"] as $propertyID):?>
						<div class="field" <? if (array_key_exists($propertyID, $arHidden)) echo 'style="display: none;"';?>>
							
								<?
								if (intval($propertyID) > 0)
								{
									if (
										$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "T"
										&&
										$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] == "1"
									)
										$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "S";
									elseif (
										(
											$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "S"
											||
											$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "N"
										)
										&&
										$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] > "1"
									)
										$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "T";
								}
								elseif (($propertyID == "TAGS") && CModule::IncludeModule('search'))
									$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "TAGS";

								if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y")
								{
									$inputNum = ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) ? count($arResult["ELEMENT_PROPERTIES"][$propertyID]) : 0;
									$inputNum += $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE_CNT"];
								}
								else
								{
									$inputNum = 1;
								}

								if($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"])
									$INPUT_TYPE = "USER_TYPE";
								else
									$INPUT_TYPE = $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"];

								switch ($INPUT_TYPE):
									case "USER_TYPE":
										for ($i = 0; $i<$inputNum; $i++)
										{
											if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
											{
												$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["~VALUE"] : $arResult["ELEMENT"][$propertyID];
												$description = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["DESCRIPTION"] : "";
											}
											elseif ($i == 0)
											{
												$value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
												$description = "";
											}
											else
											{
												$value = "";
												$description = "";
											}
											?>
											<input type="text" class="input-style" placeholder="Ф.И.О." name="<?="PROPERTY[".$propertyID."][".$i."][VALUE]"?>" value="<?=$value?>">
											<?
											$GLOBALS["APPLICATION"]->IncludeComponent(
												'bitrix:main.calendar',
												'calendar_templ',
												array(
													'FORM_NAME' => "iblock_add",
													'INPUT_NAME' => "PROPERTY[".$propertyID."][".$i."][VALUE]",
													'DESCRIPTION' => "PROPERTY[".$propertyID."][".$i."][DESCRIPTION]",
													'INPUT_VALUE' => $value,
													'SHOW_TIME' => "Y",
												),
												null,
												array('HIDE_ICONS' => 'Y')
											);
										?><?
										}
									break;
									case "TAGS":
										$APPLICATION->IncludeComponent(
											"bitrix:search.tags.input",
											"",
											array(
												"VALUE" => $arResult["ELEMENT"][$propertyID],
												"NAME" => "PROPERTY[".$propertyID."][0]",
												"TEXT" => 'size="'.$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"].'"',
											), null, array("HIDE_ICONS"=>"Y")
										);
										break;
									case "HTML":
										$LHE = new CLightHTMLEditor;
										$LHE->Show(array(
											'id' => preg_replace("/[^a-z0-9]/i", '', "PROPERTY[".$propertyID."][0]"),
											'width' => '100%',
											'height' => '200px',
											'inputName' => "PROPERTY[".$propertyID."][0]",
											'content' => $arResult["ELEMENT"][$propertyID],
											'bUseFileDialogs' => false,
											'bFloatingToolbar' => false,
											'bArisingToolbar' => false,
											'toolbarConfig' => array(
												'Bold', 'Italic', 'Underline', 'RemoveFormat',
												'CreateLink', 'DeleteLink', 'Image', 'Video',
												'BackColor', 'ForeColor',
												'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull',
												'InsertOrderedList', 'InsertUnorderedList', 'Outdent', 'Indent',
												'StyleList', 'HeaderList',
												'FontList', 'FontSizeList',
											),
										));
										break;
									case "T":
										for ($i = 0; $i<$inputNum; $i++)
										{

											if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
											{
												$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
											}
											elseif ($i == 0)
											{
												$value = intval($propertyID) > 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
											}
											else
											{
												$value = "";
											}
										?>
								<textarea cols="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]?>" rows="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"]?>" name="PROPERTY[<?=$propertyID?>][<?=$i?>]" class="textarea-style" placeholder="Текст сообщения*"><?=$value?></textarea>
										<?
										}
									break;

									case "S":
									case "N":
										for ($i = 0; $i<$inputNum; $i++)
										{
											if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
											{
												$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
											}
											elseif ('NAME' == $propertyID) {
												$value = 'Новый отзыв';
											}
											elseif ($i == 0)
											{
												$value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];

											}
											else
											{
												$value = "";
											}
										?>
										<input class="input-style" <?if ($arResult['PROPERTY_LIST_FULL'][$propertyID]['CODE'] == 'MARK') echo 'id="rating_value"';?> type="text" name="PROPERTY[<?=$propertyID?>][<?=$i?>]" size="25" value="<?=$value?>" placeholder='<?if (intval($propertyID) > 0):?><?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?><?else:?><?=!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID)?><?endif?><?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?>*<?endif?>'  />
										<?
										if($arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] == "DateTime"):?><?
											$APPLICATION->IncludeComponent(
												'bitrix:main.calendar',
												'',
												array(
													'FORM_NAME' => 'iblock_add',
													'INPUT_NAME' => "PROPERTY[".$propertyID."][".$i."]",
													'INPUT_VALUE' => $value,
												),
												null,
												array('HIDE_ICONS' => 'Y')
											);
											?><small><?=GetMessage("IBLOCK_FORM_DATE_FORMAT")?><?=FORMAT_DATETIME?></small><?
										endif
										?><?
										}
									break;

									case "F":
										for ($i = 0; $i<$inputNum; $i++)
										{
											$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
											?>
								<input type="hidden" name="PROPERTY[<?=$propertyID?>][<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>]" value="<?=$value?>" />
								<input type="file" size="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]?>"  name="PROPERTY_FILE_<?=$propertyID?>_<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>" />
											<?

											if (!empty($value) && is_array($arResult["ELEMENT_FILES"][$value]))
											{
												?>
							<input type="checkbox" name="DELETE_FILE[<?=$propertyID?>][<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>]" id="file_delete_<?=$propertyID?>_<?=$i?>" value="Y" /><label for="file_delete_<?=$propertyID?>_<?=$i?>"><?=GetMessage("IBLOCK_FORM_FILE_DELETE")?></label>
												<?

												if ($arResult["ELEMENT_FILES"][$value]["IS_IMAGE"])
												{
													?>
							<img src="<?=$arResult["ELEMENT_FILES"][$value]["SRC"]?>" height="<?=$arResult["ELEMENT_FILES"][$value]["HEIGHT"]?>" width="<?=$arResult["ELEMENT_FILES"][$value]["WIDTH"]?>" border="0" />
													<?
												}
												else
												{
													?>
							<?=GetMessage("IBLOCK_FORM_FILE_NAME")?>: <?=$arResult["ELEMENT_FILES"][$value]["ORIGINAL_NAME"]?>
							<?=GetMessage("IBLOCK_FORM_FILE_SIZE")?>: <?=$arResult["ELEMENT_FILES"][$value]["FILE_SIZE"]?>
							[<a href="<?=$arResult["ELEMENT_FILES"][$value]["SRC"]?>"><?=GetMessage("IBLOCK_FORM_FILE_DOWNLOAD")?></a>]
													<?
												}
											}
										}

									break;
									case "L":

										if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["LIST_TYPE"] == "C")
											$type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "checkbox" : "radio";
										else
											$type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "multiselect" : "dropdown";

										switch ($type):
											case "checkbox":
											case "radio":

												foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
												{
													$checked = false;
													if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
													{
														if (is_array($arResult["ELEMENT_PROPERTIES"][$propertyID]))
														{
															foreach ($arResult["ELEMENT_PROPERTIES"][$propertyID] as $arElEnum)
															{
																if ($arElEnum["VALUE"] == $key) {$checked = true; break;}
															}
														}
													}
													else
													{
														if ($arEnum["DEF"] == "Y") $checked = true;
													}

													?>
									<input type="<?=$type?>" name="PROPERTY[<?=$propertyID?>]<?=$type == "checkbox" ? "[".$key."]" : ""?>" value="<?=$key?>" id="property_<?=$key?>"<?=$checked ? " checked=\"checked\"" : ""?> /><label for="property_<?=$key?>"><?=$arEnum["VALUE"]?></label>
													<?
												}
											break;

											case "dropdown":
											case "multiselect":
											?>
									<select name="PROPERTY[<?=$propertyID?>]<?=$type=="multiselect" ? "[]\" size=\"".$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"]."\" multiple=\"multiple" : ""?>">
											<?
												if (intval($propertyID) > 0) $sKey = "ELEMENT_PROPERTIES";
												else $sKey = "ELEMENT";

												foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
												{
													$checked = false;
													if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
													{
														foreach ($arResult[$sKey][$propertyID] as $elKey => $arElEnum)
														{
															if ($key == $arElEnum["VALUE"]) {$checked = true; break;}
														}
													}
													else
													{
														if ($arEnum["DEF"] == "Y") $checked = true;
													}
													?>
										<option value="<?=$key?>" <?=$checked ? " selected=\"selected\"" : ""?>><?=$arEnum["VALUE"]?></option>
													<?
												}
											?>
									</select>
											<?
											break;

										endswitch;
									break;
								endswitch;?>
							
						</div>
					<?endforeach;?>
					<?if($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0):?>
						<table>
							<tr>
								<td><?=GetMessage("IBLOCK_FORM_CAPTCHA_TITLE")?></td>
								<td>
									<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
									<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
								</td>
							</tr>
							<tr>
								<td><?=GetMessage("IBLOCK_FORM_CAPTCHA_PROMPT")?><span class="starrequired">*</span>:</td>
								<td><input type="text" name="captcha_word" maxlength="50" value=""></td>
							</tr>
						</table>
					<?endif?>
				
				<?endif?>

				<div class="estimate-work">
                    <div class="estimate-work-txt">Оцените нашу работу</div>
                    <div class="controls">
                        <div class="stars form_stars">
                            <ul>
                                <li data-num="1" class="star star1">
                                    <a href="#" onclick="return false;"><span class="sprite star-small"></span></a>
                                </li><li data-num="2" class="star star2">
                                    <a href="#" onclick="return false;"><span class="sprite star-small"></span></a>
                                </li><li data-num="3" class="star star3">
                                    <a href="#" onclick="return false;"><span class="sprite star-small"></span></a>
                                </li><li data-num="4" class="star star4">
                                    <a href="#" onclick="return false;"><span class="sprite star-small"></span></a>
                                </li><li data-num="5" class="star star5">
                                    <a href="#" onclick="return false;"><span class="sprite star-small"></span></a>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="submit-message">
					<?$ar_city = CUriCity::get_instance()->get_current_element()?>
					<input type="hidden" name="PROPERTY[33][0]" value="<?= $ar_city['id']?>">
                  <input type="submit" value="Добавить отзыв" name="iblock_submit1" class="button yellow">
                </div>

				<?if (strlen($arParams["LIST_URL"]) > 0 && $arParams["ID"] > 0):?><input type="submit" name="iblock_apply" value="<?=GetMessage("IBLOCK_FORM_APPLY")?>" /><?endif?>

			<?if (strlen($arParams["LIST_URL"]) > 0):?><a href="<?=$arParams["LIST_URL"]?>"><?=GetMessage("IBLOCK_FORM_BACK")?></a><?endif?>
		</form>
</div>

    </div>