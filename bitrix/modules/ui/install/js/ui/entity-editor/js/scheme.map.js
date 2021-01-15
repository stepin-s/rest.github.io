{"version":3,"sources":["scheme.js"],"names":["BX","namespace","UI","EntityScheme","this","_id","_settings","_elements","_availableElements","prototype","initialize","id","settings","type","isNotEmptyString","util","getRandomString","i","length","currentData","prop","getArray","push","EntitySchemeElement","create","availableData","getId","getElements","concat","findElementByName","name","options","isRecursive","getBoolean","findElementByNameRecursive","element","getName","elements","Array","isArray","result","getAvailableElements","setAvailableElements","self","_name","_type","_title","_originalTitle","_optionFlags","_options","_isEditable","_isShownAlways","_isTransferable","_isContextMenuEnabled","_isRequired","_isRequiredConditionally","_isHeading","_isMergeable","_visibilityPolicy","EntityEditorVisibilityPolicy","always","_data","_parent","getString","getObject","prepareAdditionalParameters","_isTitleEnabled","getDataBooleanParam","_isDragEnabled","parse","title","originalTitle","getInteger","elementData","l","fieldInfo","ADDITIONAL","undefined","USER_TYPE_ID","EntityUserFieldType","file","userFieldFileUrlTemplate","template","replace","ENTITY_VALUE_ID","FIELD","URL_TEMPLATE","mergeSettings","mergeEx","getType","getTitle","setTitle","getOriginalTitle","hasCustomizedTitle","resetOriginalTitle","getOptionFlags","setOptionFlags","flags","areAttributesEnabled","isEditable","isShownAlways","isTransferable","isRequired","isRequiredConditionally","isContextMenuEnabled","isTitleEnabled","isDragEnabled","isHeading","isMergeable","needShowTitle","isVirtual","getCreationPlaceholder","getChangePlaceholder","getVisibilityPolicy","getData","setData","data","getDataParam","defaultval","get","setDataParam","val","getDataStringParam","getDataIntegerParam","getDataObjectParam","getDataArrayParam","setElements","getAffectedFields","results","getParent","setParent","parent","hasAttributeConfiguration","attributeTypeId","getAttributeConfiguration","configs","config","EntityFieldAttributeType","clone","setAttributeConfiguration","typeId","index","splice","removeAttributeConfiguration","setVisibilityConfiguration","removeVisibilityConfiguration","createConfigItem"],"mappings":"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","file":"scheme.map.js"}