<?php

require_once 'RCCWP_Constant.php';
require_once 'tools/debug.php';

/**
 * Get number of group duplicates given field name. The function returns 1
 * if there are no duplicates (just the original group), 2 if there is one
 * duplicate and so on.
 *
 * @param string $fieldName the name of any field in the group
 * @return number of group duplicates 
 */
function getGroupDuplicates ($fieldName) {
	require_once("RCCWP_CustomField.php");
	global $post;
	return RCCWP_CustomField::GetFieldGroupDuplicates($post->ID, $fieldName);
}

/**
 * Get number of field duplicates given field name and group duplicate index.
 * The function returns 1 if there are no duplicates (just the original field), 
 * 2 if there is one duplicate and so on.
 *
 * @param string $fieldName
 * @param integer $groupIndex
 * @return number of field duplicates
 */
function getFieldDuplicates ($fieldName, $groupIndex) {
	require_once("RCCWP_CustomField.php");
	global $post;
	return RCCWP_CustomField::GetFieldDuplicates($post->ID, $fieldName, $groupIndex);
}

/**
 * Get the value of an input field.
 *
 * @param string $fieldName
 * @param integer $groupIndex
 * @param integer $fieldIndex
 * @param boolean $readyForEIP if true and the field type is textbox or
 * 				multiline textbox, the resulting value will be wrapped
 * 				in a div that is ready for EIP. The default value is true
 * @return a string or array based on field type
 */
function get ($fieldName, $groupIndex=1, $fieldIndex=1, $readyForEIP=true) {
	require_once("RCCWP_CustomField.php");
	global $wpdb, $post, $FIELD_TYPES;
	
	$field = RCCWP_CustomField::GetInfoByName($fieldName);
	if(!$field) return FALSE;
	
	$fieldType = $field['type'];
	$fieldID = $field['id'];
	$fieldObject = $field['properties'];
	
	$single = true;
	switch($fieldType){
		case $FIELD_TYPES["checkbox_list"]:
		case $FIELD_TYPES["listbox"]:
			$single = false;
			break;
	} 
	
	$fieldValues = (array) RCCWP_CustomField::GetValues($single, $post->ID, $fieldName, $groupIndex, $fieldIndex);
    if(empty($fieldValues)) return FALSE;

	$results = GetProcessedFieldValue($fieldValues, $fieldType, $fieldObject);
    
	//filter for multine line
	if($fieldType == $FIELD_TYPES['multiline_textbox']){
		$results = apply_filters('the_content', $results);
	}
	if($fieldType == $FIELD_TYPES['image']){
		$results = split('&',$results);
		$results = $results[0];
	}
	
	// Prepare fields for EIP 
	include_once('RCCWP_Options.php');
	$enableEditnplace = RCCWP_Options::Get('enable-editnplace');
	if ($readyForEIP && $enableEditnplace == 1 && current_user_can('edit_posts', $post->ID)){
	
	    switch($fieldType){
	        case $FIELD_TYPES["textbox"]:
			if(!$results) $results="&nbsp";
			$results = "<div class='".EIP_textbox($fieldMetaID)."' >".$results."</div>";
			break;

	        case $FIELD_TYPES["multiline_textbox"]:
			if(!$results) $results="&nbsp";
			$results = "<div class='".EIP_mulittextbox($fieldMetaID)."' >".$results."</div>";
			break;
        }

    }
    return $results;

}

function GetProcessedFieldValue($fieldValues, $fieldType, $fieldProperties=array()){
	global $FIELD_TYPES;
	
	$results = array();
	$fieldValues = (array) $fieldValues;
	foreach($fieldValues as $fieldValue){
	
		switch($fieldType){
			case $FIELD_TYPES["audio"]:
			case $FIELD_TYPES["file"]:
			case $FIELD_TYPES["image"]:
				if ($fieldValue != "") $fieldValue = MF_FILES_URI.$fieldValue;
				break;
	
			case $FIELD_TYPES["checkbox"]: 		
				if ($fieldValue == 'true')  $fieldValue = true; else $fieldValue = false; 
				break;
	
			case $FIELD_TYPES["date"]: 
				$fieldValue = date($fieldProperties['format'],strtotime($fieldValue)); 
				break;
		}
		
		array_push($results, $fieldValue); 
	}
	
	// Return array or single value based on field
	switch($fieldType){
		case $FIELD_TYPES["checkbox_list"]:
		case $FIELD_TYPES["listbox"]:
			return $results;
		 	break;
	}

	if (count($results) == 0 )
		return "";
	else
		return $results[0];
}

// Get Image. 
function get_image ($fieldName, $groupIndex=1, $fieldIndex=1,$tag_img=1) {
	require_once("RCCWP_CustomField.php");
	global $wpdb, $post;
	
	$field = RCCWP_CustomField::GetInfoByName($fieldName);
	if(!$field) return FALSE;
	
	$fieldType = $field['type'];
	$fieldID = $field['id'];
	$fieldCSS = $field['CSS'];
	$fieldObject = $field['properties'];
	
	$fieldValues = (array) RCCWP_CustomField::GetValues(true, $post->ID, $fieldName, $groupIndex, $fieldIndex);
    if(empty($fieldValues)) return FALSE;

	if(!empty($fieldValues[0]))
		$fieldValue = $fieldValues[0];
	else 
		return "";
	
	if (substr($fieldObject['params'], 0, 1) == "?"){
			$fieldObject['params'] = substr($fieldObject['params'], 1);
		}
	
	 //check if exist params, if not exist params, return original image
	if (empty($fieldObject['params']) && (FALSE == strstr($fieldValue, "&"))){
		$fieldValue = MF_FILES_URI.$fieldValue;
	}else{
		//check if exist thumb image, if exist return thumb image
		$md5_params = md5($fieldObject['params']);
		if (file_exists(MF_FILES_PATH.'th_'.$md5_params."_".$fieldValue)) {
			$fieldValue = MF_FILES_URI.'th_'.$md5_params."_".$fieldValue;
		}else{
			//generate thumb
			//include_once(MF_URI_RELATIVE.'thirdparty/phpthumb/phpthumb.class.php');
			include_once(dirname(__FILE__)."/thirdparty/phpthumb/phpthumb.class.php");
			$phpThumb = new phpThumb();
			$phpThumb->setSourceFilename(MF_FILES_PATH.$fieldValue);
			$create_md5_filename = 'th_'.$md5_params."_".$fieldValue;
			$output_filename = MF_FILES_PATH.$create_md5_filename;
			$final_filename = MF_FILES_URI.$create_md5_filename;

			$params_image = explode("&",$fieldObject['params']);
			foreach($params_image as $param){
				if($param){
					$p_image=explode("=",$param);
					$phpThumb->setParameter($p_image[0], $p_image[1]);
				}
			}
			if ($phpThumb->GenerateThumbnail()) {
				if ($phpThumb->RenderToFile($output_filename)) {
					$fieldValue = $final_filename;
				}
			}
		}
	}
	
	if($tag_img){
		if (empty($fieldCSS)){
			$finalString = stripslashes(trim("\<img src=\'".$fieldValue."\' /\>"));
		}else{
			$finalString = stripslashes(trim("\<img src=\'".$fieldValue."\' class=\"".$fieldCSS."\" \/\>"));
		}
	}else{
		$finalString=$fieldValue;
	}
	return $finalString;
}

// generate image
function gen_image ($fieldName, $groupIndex=1, $fieldIndex=1,$param=NULL,$attr=NULL) {
	require_once("RCCWP_CustomField.php");
	global $wpdb, $post;
	
	$field = RCCWP_CustomField::GetInfoByName($fieldName);
	if(!$field) return FALSE;
	
	$fieldType = $field['type'];
	$fieldID = $field['id'];
	$fieldCSS = $field['CSS'];
	$fieldObject = $field['properties'];
	
	$fieldValue = RCCWP_CustomField::GetValues(true, $post->ID, $fieldName, $groupIndex, $fieldIndex);
    if(empty($fieldValue)) return FALSE;
	
	 //check if exist params, if not exist params, return original image
	if (!count($param)){
		$fieldValue = MF_FILES_URI.$fieldValue;
	}else{
		//check if exist thumb image, if exist return thumb image
		$name_md5="";
		foreach($param as $k => $v){
			$name_md5.= $k."=".$v;
		}
		$md5_params = md5($name_md5);
		if (file_exists(MF_FILES_PATH.'th_'.$md5_params."_".$fieldValue)) {
			$fieldValue = MF_FILES_URI.'th_'.$md5_params."_".$fieldValue;
		}else{
			//generate thumb
			include_once(dirname(__FILE__)."/thirdparty/phpthumb/phpthumb.class.php");
			$phpThumb = new phpThumb();
			$phpThumb->setSourceFilename(MF_FILES_PATH.$fieldValue);
			$create_md5_filename = 'th_'.$md5_params."_".$fieldValue;
			$output_filename = MF_FILES_PATH.$create_md5_filename;
			$final_filename = MF_FILES_URI.$create_md5_filename;

			foreach($param as $k => $v){
					$phpThumb->setParameter($k, $v);
			}
			if ($phpThumb->GenerateThumbnail()) {
				if ($phpThumb->RenderToFile($output_filename)) {
					$fieldValue = $final_filename;
				}
			}
		}
	}
	
	if(count($attr)){
		foreach($attr as $k => $v){
			$add_attr .= $k."='".$v."' ";
		}
		$finalString = "<img src='".$fieldValue."' ".$add_attr." />";
	}else{
		$finalString = "<img src='".$fieldValue."' />";
	}
	
	return $finalString;
}


// Get Audio. 
function get_audio ($fieldName, $groupIndex=1, $fieldIndex=1) {
	require_once("RCCWP_CustomField.php");
	global $wpdb, $post;
	
	$field = RCCWP_CustomField::GetInfoByName($fieldName);
	if(!$field) return FALSE;
	
	$fieldType = $field['type'];
	$fieldID = $field['id'];
	
	$fieldValues = (array) RCCWP_CustomField::GetValues(true, $post->ID, $fieldName, $groupIndex, $fieldIndex);
    if(empty($fieldValues)) return FALSE;
	
	if(!empty($fieldValues))
		$fieldValue = $fieldValues[0];
	else 
		return "";
		
	$path = MF_FILES_URI;
	$fieldValue = $path.$fieldValue;
	$finalString = stripslashes(trim("\<div style=\'padding-top:3px;\'\>\<object classid=\'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\' codebase='\http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0\' width=\'95%\' height=\'20\' wmode=\'transparent\' \>\<param name=\'movie\' value=\'".MF_URI."js/singlemp3player.swf?file=".urlencode($fieldValue)."\' wmode=\'transparent\' /\>\<param name=\'quality\' value=\'high\' wmode=\'transparent\' /\>\<embed src=\'".MF_URI."js/singlemp3player.swf?file=".urlencode($fieldValue)."' width=\'50\%\' height=\'20\' quality=\'high\' pluginspage=\'http://www.macromedia.com/go/getflashplayer\' type=\'application/x-shockwave-flash\' wmode=\'transparent\' \>\</embed\>\</object\>\</div\>"));
	return $finalString;
}

function GetFieldInfo($customFieldId)
	{
		global $wpdb;
		$sql = "SELECT properties FROM " . MF_TABLE_CUSTOM_FIELD_PROPERTIES  .
			" WHERE custom_field_id = '" . $customFieldId."'";
		$results = $wpdb->get_row($sql);
		//$results->options = unserialize($results->options);
		$results->properties = unserialize($results->properties);
		//$results->default_value = unserialize($results->default_value);
		return $results;
	}
        
function pt(){
    return PHPTHUMB;
}


/**
 * Return a array with the order of a group
 *
 * @param string $groupName 
 */
function getGroupOrder($field_name){
    global $post,$wpdb;
    
    $elements  = $wpdb->get_results("SELECT group_count FROM ".MF_TABLE_POST_META." WHERE post_id = ".$post->ID."  AND field_name = '{$field_name}' ORDER BY order_id ASC");
   
    foreach($elements as $element){
       $order[] =  $element->group_count;
    }
     
    return $order;
}

/**
 *  Return a array with the order of a  field
 */
function getFieldOrder($field_name,$group){ 
	global $post,$wpdb; 
	
	$elements = $wpdb->get_results("SELECT field_count FROM ".MF_TABLE_POST_META." WHERE post_id = ".$post->ID." AND field_name = '{$field_name}' AND group_count = {$group} ORDER BY order_id DESC",ARRAY_A);  

	foreach($elements as $element){ 
		$order[] = $element['field_count']; 
	} 

	$order = array_reverse($order); 
 	sort($order); 

	return $order; 
}
?>