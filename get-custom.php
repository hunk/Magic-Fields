<?php

require_once 'MF_Constant.php';
require_once 'tools/debug.php';

/*
 * THE CACHE FUNCTIONS
 * by martin@attitude.sk 
 *
 * When online you can end up with tons of get requests in theme files and site tends to get slow.
 * The simple caching mechanism makes it like 6-times faster depending on your server configuration.
 *
 */

	/*
	 * Function to return data if not older than Time To Live
	 * 
	 * @param string $file - Relative path to cached folder
	 * @param int $ttl - Duration in seconds file is considered to be up to date. Defaut is 5 minutes.
	 */
	function MF_get_cached_data( $file, $ttl = 300 ) {
		if( !MF_GET_CACHE_IS_ON ) return FALSE;
		if( file_exists( MF_GET_CACHE_DIR . $file ) ) {
			// If you set $ttl to FALSE or negative value, no mod. file time is checked
			if( !$ttl || $ttl <= 0 ) {
				return file_get_contents( MF_GET_CACHE_DIR . $file );
			}
			
			if( ( time() - filemtime( MF_GET_CACHE_DIR . $file ) ) < $ttl ) {
				return file_get_contents( MF_GET_CACHE_DIR . $file );
			}
		}
	return FALSE;
	}
	
	/*
	 * Function to cache data to specified file
	 * 
	 * @param string $file - Relative path to cached folder
	 * @param string $data - String data to store.
	 */
	function MF_put_cached_data( $file, $data ) {
		if( !MF_GET_CACHE_IS_ON ) return FALSE;
		if( !file_exists( dirname( MF_GET_CACHE_DIR . $file ) ) ) {
			// Recursion to create directories
			if( !mkdir( dirname( MF_GET_CACHE_DIR . $file ), 0777, TRUE) ) {
				return FALSE;
			}
		}
		if( file_put_contents( MF_GET_CACHE_DIR . $file, $data ) ) {
			return TRUE;
		}else {
			return FALSE;
		}
	}

	/*
	 * Purge any files and folder in directiory.
	 *
	 * @param string $dir - absolute path to the dir
	 *
	 */
	function purge_cache_dir($dir) {
		foreach (glob($dir) as $file) {
			if (is_dir($file)) { 
				purge_cache_dir("$file/*");
				rmdir($file);
			} else {
				unlink($file);
			}
		}
	}


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
function get ($fieldName, $groupIndex=1, $fieldIndex=1, $readyForEIP=true,$post_id=NULL) {
	require_once("RCCWP_CustomField.php");
	global $post, $FIELD_TYPES;
	
	if(!$post_id){ $post_id = $post->ID; }
	
	$cache_name = $post_id.'/'.$fieldName.'--'.$groupIndex.'--'.$fieldIndex.'.txt';
	$field = unserialize( MF_get_cached_data( $cache_name, FALSE ) );
	
	// When field is set, but it's empty, it gets a NULL value, but still this value is cached
	// therefore: if !is_null condition
	if( !$field && !is_null( $field ) ) {
		$field = RCCWP_CustomField::GetDataField($fieldName,$groupIndex, $fieldIndex,$post_id);
		MF_put_cached_data( $cache_name, serialize( $field ) );
	}
	if(!$field) return FALSE;
	
	$fieldType = $field['type'];
	$fieldID = $field['id'];
	$fieldObject = $field['properties'];
	$fieldValues = (array)$field['meta_value'];
	$fieldMetaID = $field['meta_id'];

	$results = GetProcessedFieldValue($fieldValues, $fieldType, $fieldObject);
	
	//filter for multine line
	if($fieldType == $FIELD_TYPES['multiline_textbox']){
          if( !RCCWP_Options::Get('dont-remove-tmce') ){
            $results = apply_filters('the_content', $results);
          }
	}
	
	if($fieldType == $FIELD_TYPES['image']){
		$results = preg_split("/&/",$results);
		$results = $results[0];
	}

  if($fieldType == $FIELD_TYPES['file'] || $fieldType == $FIELD_TYPES['image']) {
    $results = apply_filters('mf_source_image',$results);
  }

	return $results;

}


function get_clean($fieldName, $groupIndex=1, $fieldIndex=1, $readyForEIP=true,$post_id=NULL) {
	require_once("RCCWP_CustomField.php");
	global $post, $FIELD_TYPES;
	
	if(!$post_id){ $post_id = $post->ID; }
	$cache_name = $post_id.'/_clean-'.$fieldName.'--'.$groupIndex.'--'.$fieldIndex.'.txt';

	$field = unserialize( MF_get_cached_data( $cache_name, FALSE ) );
	
	// When field is set, but it's empty, it gets a NULL value, but still this value is cached
	// therefore: if !is_null condition
	if( !$field && !is_null( $field ) ) {
		$field = RCCWP_CustomField::GetDataField($fieldName,$groupIndex, $fieldIndex,$post_id);
		MF_put_cached_data( $cache_name, serialize( $field ) );
	}
	if(!$field) return FALSE;
	
	$fieldType = $field['type'];
	$fieldID = $field['id'];
	$fieldObject = $field['properties'];
	$fieldValues = (array)$field['meta_value'];
	$fieldMetaID = $field['meta_id'];
	
	if($fieldType != $FIELD_TYPES['multiline_textbox']) return FALSE;
	
	$results = GetProcessedFieldValue($fieldValues, $fieldType, $fieldObject);
	
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
			  if(!$fieldValue) return false;
				$fieldValue = date($fieldProperties['format'],strtotime($fieldValue)); 
				break;
		  case $FIELD_TYPES["Image (Upload Media)"]:
				if ($fieldValue != ""){ 
					$data = wp_get_attachment_image_src($fieldValue,'original');
					$fieldValue = $data[0];
		    	}
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

// Get Audio. 
function get_audio ($fieldName, $groupIndex=1, $fieldIndex=1,$post_id=NULL) {
	require_once("RCCWP_CustomField.php");
	global $post;
	
	if(!$post_id){ $post_id = $post->ID; }
	$field = RCCWP_CustomField::GetDataField($fieldName,$groupIndex, $fieldIndex,$post_id);
	if(!$field) return FALSE;
	$fieldType = $field['type'];
	$fieldID = $field['id'];
	$fieldValue = $field['meta_value'];
	
	if(empty($fieldValue)) return FALSE;
		
	$path = MF_FILES_URI;
	$fieldValue = $path.$fieldValue;
	$finalString = stripslashes(trim("\<div style=\'padding-top:3px;\'\>\<object classid=\'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\' codebase='\http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0\' width=\'95%\' height=\'20\' wmode=\'transparent\' \>\<param name=\'movie\' value=\'".MF_URI."js/singlemp3player.swf?file=".urlencode($fieldValue)."\' wmode=\'transparent\' /\>\<param name=\'quality\' value=\'high\' wmode=\'transparent\' /\>\<embed src=\'".MF_URI."js/singlemp3player.swf?file=".urlencode($fieldValue)."' width=\'50\%\' height=\'20\' quality=\'high\' pluginspage=\'http://www.macromedia.com/go/getflashplayer\' type=\'application/x-shockwave-flash\' wmode=\'transparent\' \>\</embed\>\</object\>\</div\>"));
	return $finalString;
}

function GetFieldInfo($customFieldId){
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
function getGroupOrder($field_name,$post_id=NULL){
	global $post,$wpdb;

	if(!$post_id){ $post_id = $post->ID; }
	$elements  = $wpdb->get_results("SELECT DISTINCT(group_count) FROM ".MF_TABLE_POST_META." WHERE post_id = ".$post_id."  AND field_name = '{$field_name}' ORDER BY order_id ASC");
   
	foreach($elements as $element){
		$order[] =  $element->group_count;
	}
	 
	return $order;
}

/**
 *  Return a array with the order of a  field
 */
function getFieldOrder($field_name,$group=1,$post_id=NULL){ 
	global $post,$wpdb; 
	
	if(!$post_id){ $post_id = $post->ID; }
	$elements = $wpdb->get_results("SELECT field_count FROM ".MF_TABLE_POST_META." WHERE post_id = ".$post_id." AND field_name = '{$field_name}' AND group_count = {$group} ORDER BY order_id DESC",ARRAY_A);  

	foreach($elements as $element){ 
		$order[] = $element['field_count']; 
	} 

  if( !isset($order) || is_null($order) ) {
    return array(); 
  }

	$order = array_reverse($order); 
 	sort($order); 

	return $order; 
}
/**
 * Return the name of the write panel the current post uses
 * 
 * @param boolean $safe make the return name 'url safe'
 */
function get_panel_name($safe=true, $post_id = NULL){
	global $wpdb, $post;
  
  if (!$post_id) {
    $post_id = $post->ID;
  }
  
	$panel_id = $wpdb->get_var("SELECT `meta_value` FROM {$wpdb->postmeta} WHERE post_id = ".$post_id.' AND meta_key = "'.RC_CWP_POST_WRITE_PANEL_ID_META_KEY.'"');
	if( (int) $panel_id == 0 )
		return false;
	
	$panel_name = $wpdb->get_var("SELECT `name` FROM ".MF_TABLE_PANELS." WHERE id = ".$panel_id);
	if( ! $panel_name )
		return false;

	return ($safe) ? sanitize_title_with_dashes($panel_name) : $panel_name;
}

// Get Image. 
function get_image ($fieldName, $groupIndex=1, $fieldIndex=1,$tag_img=1,$post_id=NULL,$override_params=NULL, $wp_size='original') {
	return create_image(array(
		'fieldName' => $fieldName, 
		'groupIndex' => $groupIndex, 
		'fieldIndex' => $fieldIndex,
		'param' => $override_params,
		'post_id' => $post_id,
		'tag_img' => (boolean) $tag_img,
		'wp_size' => $wp_size
	));
}

// generate image
function gen_image ($fieldName, $groupIndex=1, $fieldIndex=1,$param=NULL,$attr=NULL,$post_id=NULL) {
	return create_image(array(
		'fieldName' => $fieldName, 
		'groupIndex' => $groupIndex, 
		'fieldIndex' => $fieldIndex,
		'param' => $param,
		'attr' => $attr,
		'post_id' => $post_id
	));
}

/*
 * Generate an image from a field value
 *
 * Accepts a single options, an array of settings. 
 * These are the parameteres it supports:
 *
 *   'fieldName' => (string) the name of the field which holds the image value, 
 *   'groupIndex' => (int) which group set to display, 
 *   'fieldIndex' => (int) which field set to display,
 *   'param' => (string|array) a html parameter string to use with PHPThumb for the image, can also be a key/value array
 *   'attr' => (array) an array of extra attributes and values for the image tag,
 *   'post_id' => (int) a specific post id to fetch,
 *   'tag_img' => (boolean) a flag to determine if an img tag should be created, or just return the link to the image file
 *
 */
function create_image($options)
{
	require_once("RCCWP_CustomField.php");
	global $post;
	
	// establish the default values, then override them with 
	// whatever the user has passed in
	$options = array_merge(array(
		// the default options
		'fieldName' => '', 
		'groupIndex' => 1, 
		'fieldIndex' => 1,
		'param' => NULL,
		'attr' => NULL,
		'post_id' => NULL,
		'tag_img' => true,
		'wp_size' => 'original'
	), (array) $options);
	
	// finally extract them into variables for this function
	extract($options);
	
	// check for a specified post id, or see if the $post global has one
	if($post_id){
		$post_id = $post_id;
	}elseif(isset($post->ID)){
		$post_id = $post->ID;
	} else {
		return false;
	}
	
	// basic check
	if(empty($fieldName)) return FALSE;
	
	$field = RCCWP_CustomField::GetDataField($fieldName,$groupIndex, $fieldIndex,$post_id);
	if(!$field) return FALSE;
	
	$fieldType = $field['type'];
	$fieldID = $field['id'];
	$fieldCSS = $field['CSS'];
	$fieldObject = $field['properties'];
	$fieldValue = $field['meta_value'];

  if($fieldType == 16){
    $data = wp_get_attachment_image_src($fieldValue, $wp_size);
    $fieldValue = $data[0];
  }

	if(empty($fieldValue)) return "";
	
	// override the default phpthumb parameters if needed
	// works with both strings and arrays
	if(!empty($param)) {
		if(is_array($param)){
			$p = array();
			foreach($param as $k => $v){
				$p[] = $k."=".$v;
			}
			$fieldObject['params'] = implode('&', $p);
		} else {
			$fieldObject['params'] = $param;
		}
	}
	// remove the ? on the params if it happened to be there
	if( isset($fieldObject['params']) && !empty($fieldObject['params']) ){
	  if (substr($fieldObject['params'], 0, 1) == "?"){
		  $fieldObject['params'] = substr($fieldObject['params'], 1);
	  }
  }

	// check if exist params, if not exist params, return original image
	if (empty($fieldObject['params']) && (FALSE === strstr($fieldValue, "&"))){
    if($fieldType == 9){
		  $fieldValue = MF_FILES_URI.$fieldValue;
      $fieldValue = apply_filters('mf_source_image', $fieldValue);
	  }
	}else{
	  //generate or check de thumb
	  $fieldValue = aux_image($fieldValue,$fieldObject['params'],$fieldType);
	}
	if($tag_img){
		// make sure the attributes are an array
		if( !is_array($attr) ) $attr = (array) $attr;
		
		// we're generating an image tag, but there MAY be a default class. 
		// if one was defined, however, override it
		if( !isset($attr['class']) && !empty($fieldCSS) ) 
			$attr['class'] = $fieldCSS;
		
		// ok, put it together now
		if(count($attr)){
		  $add_attr = NULL;
			foreach($attr as $k => $v){
				$add_attr .= $k."='".$v."' ";
			}
			$finalString = "<img src='".$fieldValue."' ".$add_attr." />";
		}else{
			$finalString = "<img src='".$fieldValue."' />";
		}
	}else{
		$finalString = $fieldValue;
	}
	return $finalString;
}

function aux_image($fieldValue,$params_image,$fieldType = NULL){

	$md5_params = md5($params_image);
  
  $thumb_path = MF_CACHE_DIR.'th_'.$md5_params."_".$fieldValue;
  $thumb_url = MF_CACHE_URI.'th_'.$md5_params."_".$fieldValue;
  $image_path = MF_UPLOAD_FILES_DIR.$fieldValue;
  $name_image = $fieldValue;
  if($fieldType == 16){
    $data = preg_split('/\//',$fieldValue);
    $thumb_path = MF_CACHE_DIR.'th_'.$md5_params."_".$data[count($data)-1];
    $thumb_url = MF_CACHE_URI.'th_'.$md5_params."_".$data[count($data)-1];
    $image_path = str_replace(WP_CONTENT_URL.DIRECTORY_SEPARATOR,MF_WPCONTENT,$fieldValue);
    $name_image = $data[count($data)-1];
  }

  $exists = file_exists($thumb_path);
  
  list($exists, $thumb_url) = apply_filters('mf_source_path_thumb_image',array($exists, $thumb_url));

	if ($exists) {
		$fieldValue = $thumb_url;
	}else{
	//generate thumb
	$create_md5_filename = 'th_'.$md5_params."_".$name_image;
	$output_filename = MF_CACHE_DIR.$create_md5_filename;
	$final_filename = MF_CACHE_URI.$create_md5_filename;

  do_action('mf_before_generate_thumb',$image_path);

  	$default = array(
    	'zc'=> 1,
  		'w'	=> 0,
  		'h'	=> 0,
  		'q'	=>  85,
  		'src' => $image_path,
                'far' => false,
                'iar' => false
  	);

	$size = @getimagesize($image_path);
	$defaults['w'] = $size[0];
	$defaults['h'] = $size[1];

	$params_image = explode("&",$params_image);
	foreach($params_image as $param){
		if($param){
			$p_image=explode("=",$param);
			$default[$p_image[0]] = $p_image[1];
		}
	}

	if( ($default['w'] > 0) && ($default['h'] == 0) ){
	  $default['h'] = round( ($default['w']*$defaults['h']) / $defaults['w'] );
	}elseif( ($default['w'] == 0) && ($default['h'] > 0) ){
	  $default['w'] = round( ($default['h']*$defaults['w']) / $defaults['h'] );
	}
	
	$MFthumb = MF_PATH.'/MF_thumb.php';
  require_once($MFthumb);
	$thumb = new mfthumb();
	$thumb_path = $thumb->image_resize(
	  $default['src'],
	  $default['w'],
	  $default['h'],
	  $default['zc'],
          $default['far'],
          $default['iar'],
	  $output_filename,
	  $default['q']
	);
        
	  if ( is_wp_error($thumb_path) )
      return $thumb_path->get_error_message();
  
  
    $fieldValue = $final_filename;
    list($tm_width, $tm_height, $tm_type, $tm_attr) = getimagesize($output_filename);
    $file = array( 
      'tmp_name' => $output_filename,
      'size'     => filesize($output_filename),
      'type'     => $tm_type
    );

    do_action('mf_after_upload_file',$file);
    do_action('mf_save_thumb_file',$final_filename);

    $fieldValue = apply_filters('mf_source_image', $fieldValue);

  }
  return $fieldValue;
}


/* 
  Get a generated image for a field value that's already known.
  This is the case for values from the get_group function, which is the 
  fastest way to iterate over groups.
*/

function gen_image_for($value, $params_image, $fieldType = 9) {
  $name = str_replace(MF_FILES_URI, '', $value); // ensure magic fields URI is not appended.
  
  return aux_image($name, $params_image, $fieldType);
}



function get_group($name_group,$post_id=NULL){
	global $wpdb, $post, $FIELD_TYPES;
	
	if(!$post_id){ $post_id = $post->ID; }
	
	$cache_name = $post_id.'/_groups-'. sanitize_title_with_dashes( $name_group ).'.txt';
	if( !$data_groups = unserialize( MF_get_cached_data( $cache_name, FALSE ) ) ) {
		$sql = "SELECT		pm.field_name, cf.type, pm_wp.meta_value, pm.order_id, pm.field_count, cf.id, fp.properties 
				FROM 		".MF_TABLE_POST_META." pm, ".MF_TABLE_PANEL_GROUPS." g, {$wpdb->postmeta} pm_wp,
							".MF_TABLE_GROUP_FIELDS." cf 
				LEFT JOIN ".MF_TABLE_CUSTOM_FIELD_PROPERTIES." fp ON fp.custom_field_id = cf.id
				WHERE 		pm_wp.post_id = {$post_id} AND cf.name = pm.field_name AND cf.group_id=g.id AND 
							g.name='$name_group' AND pm_wp.meta_id=pm.id AND pm_wp.meta_value <> '' 
				ORDER BY 	pm.order_id, cf.display_order, pm.field_count";
			$data_groups = $wpdb->get_results($sql);
		MF_put_cached_data( $cache_name, serialize( $data_groups ) );
	}

	$info = null;
	foreach($data_groups as $data){
		switch($data->type){
			case $FIELD_TYPES["textbox"]:
			case $FIELD_TYPES["radiobutton_list"]:
			case $FIELD_TYPES["dropdown_list"]:
			case $FIELD_TYPES["color_picker"]:
			case $FIELD_TYPES["slider"]:
			case $FIELD_TYPES["related_type"]:
			case $FIELD_TYPES['markdown_textbox']:
				$info[$data->order_id][$data->field_name][$data->field_count] = $data->meta_value;
				break;
			case $FIELD_TYPES['multiline_textbox']:
				$info[$data->order_id][$data->field_name][$data->field_count] = apply_filters('the_content', $data->meta_value);
				break;
			case $FIELD_TYPES["checkbox"]: 		
					if ($data->meta_value == 'true')  $fieldValue = 1; else $fieldValue = 0;
					$info[$data->order_id][$data->field_name][$data->field_count] = $fieldValue; 
					break;
			case $FIELD_TYPES["checkbox_list"]:
			case $FIELD_TYPES["listbox"]:
					$info[$data->order_id][$data->field_name][$data->field_count] = unserialize($data->meta_value);
				break;
			case $FIELD_TYPES["audio"]:
			case $FIELD_TYPES["file"]:
				if ($data->meta_value != ""){ $fieldValue = MF_FILES_URI.$data->meta_value;}else{$fieldValue= null;}
				$info[$data->order_id][$data->field_name][$data->field_count] = $fieldValue;
				break;
			case $FIELD_TYPES['image']:
				if($data->meta_value != ""){
					$format = unserialize($data->properties);
					if($format) $info[$data->order_id][$data->field_name][$data->field_count]['t'] = aux_image($data->meta_value,$format['params']);
					$info[$data->order_id][$data->field_name][$data->field_count]['o'] = MF_FILES_URI.$data->meta_value;		
					$info[$data->order_id][$data->field_name][$data->field_count]['src'] = MF_FILES_URI.$data->meta_value;	// added a more familiar key for HTML devs!	
					$info[$data->order_id][$data->field_name][$data->field_count]['name'] = $data->meta_value; // a way to get JUST the file name, for use with aux_image
				}
				break;
		  case $FIELD_TYPES['Image (Upload Media)']:
  			if($data->meta_value != ""){
					$format = unserialize($data->properties);
  				$image = wp_get_attachment_image_src($data->meta_value,'original');
  				
  				if($format) $info[$data->order_id][$data->field_name][$data->field_count]['t'] = aux_image($image[0],$format['params'],$data->type);

  				$info[$data->order_id][$data->field_name][$data->field_count]['o'] = $image[0];		
  			}
  			break;
			case $FIELD_TYPES['date']:
				$format = unserialize($data->properties);
				$fieldValue = GetProcessedFieldValue($data->meta_value, $data->type, $format);
				$info[$data->order_id][$data->field_name][$data->field_count] = $fieldValue;
				break;
		}
	}

	return $info;
}

function get_label($fieldName,$post_id=NULL) {
	require_once("RCCWP_CustomField.php");
	global $post;
	
	if(!$post_id){ $post_id = $post->ID; }
	
	$field = RCCWP_CustomField::GetInfoByName($fieldName,$post_id);
	if(!$field) return FALSE;
	return $field['description'];
}

function get_field_duplicate($fieldName, $groupIndex=1,$post_id=NULL){
	global $wpdb, $post, $FIELD_TYPES;
	
	if(!$post_id){ $post_id = $post->ID; }
	
	$cache_name = $post_id.'/_fduplicates-'.$fieldName.'--'.$groupIndex.'.txt';
	$data_fields = unserialize( MF_get_cached_data( $cache_name, FALSE ) );
	
	// When field is set, but it's empty, it gets a NULL value, but still this value is cached
	// therefore: if !is_null condition
	if( !$data_fields && !is_null( $data_fields ) ) {
		$sql = "SELECT 		pm.field_name, cf.type, pm_wp.meta_value, pm.order_id, pm.field_count, cf.id, fp.properties 
				FROM 		".MF_TABLE_POST_META." pm, ".MF_TABLE_PANEL_GROUPS." g, {$wpdb->postmeta} pm_wp,
							".MF_TABLE_GROUP_FIELDS." cf 
				LEFT JOIN ".MF_TABLE_CUSTOM_FIELD_PROPERTIES." fp ON fp.custom_field_id = cf.id
				WHERE 		pm_wp.post_id = {$post_id} AND cf.name = pm.field_name AND cf.group_id=g.id AND
							pm_wp.meta_id=pm.id AND pm.field_name='$fieldName' AND pm.group_count = $groupIndex
							AND pm_wp.meta_value <> '' 
				ORDER BY 	pm.order_id, cf.display_order, pm.field_count";
			
		$data_fields = $wpdb->get_results($sql);
		MF_put_cached_data( $cache_name, serialize( $data_fields ) );
	}

	$info = null;
	foreach($data_fields as $data){
		switch($data->type){
			case $FIELD_TYPES["textbox"]:
			case $FIELD_TYPES["radiobutton_list"]:
			case $FIELD_TYPES["dropdown_list"]:
			case $FIELD_TYPES["color_picker"]:
			case $FIELD_TYPES["slider"]:
			case $FIELD_TYPES["related_type"]:
			case $FIELD_TYPES['markdown_textbox']:
				$info[$data->field_count] = $data->meta_value;
				break;
			case $FIELD_TYPES['multiline_textbox']:
				$info[$data->field_count] = apply_filters('the_content', $data->meta_value);
				break;
			case $FIELD_TYPES["checkbox"]: 		
					if ($data->meta_value == 'true')  $fieldValue = 1; else $fieldValue = 0;
					$info[$data->field_count] = $fieldValue; 
					break;
			case $FIELD_TYPES["checkbox_list"]:
			case $FIELD_TYPES["listbox"]:
					$info[$data->field_count] = unserialize($data->meta_value);
				break;
			case $FIELD_TYPES["audio"]:
			case $FIELD_TYPES["file"]:
				if ($data->meta_value != ""){ $fieldValue = MF_FILES_URI.$data->meta_value;}else{$fieldValue= null;}
				$info[$data->field_count] = $fieldValue;
				break;
			case $FIELD_TYPES['image']:
				if($data->meta_value != ""){
					$format = unserialize($data->properties);
					if($format) $info[$data->field_count]['t'] = aux_image($data->meta_value,$format['params']);
					$info[$data->field_count]['o'] = MF_FILES_URI.$data->meta_value;		
				}
				break;
			case $FIELD_TYPES['date']:
				$format = unserialize($data->properties);
				$fieldValue = GetProcessedFieldValue($data->meta_value, $data->type, $format);
				$info[$data->field_count] = $fieldValue;
				break;
	    case $FIELD_TYPES['Image (Upload Media)']:
  			if($data->meta_value != ""){
  				$format = unserialize($data->properties);
  				$image = wp_get_attachment_image_src($data->meta_value,'original');
  				if($format) $info[$data->order_id][$data->field_name][$data->field_count]['t'] = aux_image($image[0],$format['params'],$data->type);

    			$info[$data->order_id][$data->field_name][$data->field_count]['o'] = $image[0];		
    		}
    		break;
		}
	}
	return $info;
}

/*Added By Justin Grover to allow us to get a repeating field that is a multiline field without applying the "the_content" filter*/

function get_clean_field_duplicate($fieldName, $groupIndex=1,$post_id=NULL){
	global $wpdb, $post, $FIELD_TYPES;
	
	if(!$post_id){ $post_id = $post->ID; }
	
	$cache_name = $post_id.'/_fduplicates-'.$fieldName.'--'.$groupIndex.'.txt';
	$data_fields = unserialize( MF_get_cached_data( $cache_name, FALSE ) );
	
	// When field is set, but it's empty, it gets a NULL value, but still this value is cached
	// therefore: if !is_null condition
	if( !$data_fields && !is_null( $data_fields ) ) {
		$sql = "SELECT 		pm.field_name, cf.type, pm_wp.meta_value, pm.order_id, pm.field_count, cf.id, fp.properties 
				FROM 		".MF_TABLE_POST_META." pm, ".MF_TABLE_PANEL_GROUPS." g, {$wpdb->postmeta} pm_wp,
							".MF_TABLE_GROUP_FIELDS." cf 
				LEFT JOIN ".MF_TABLE_CUSTOM_FIELD_PROPERTIES." fp ON fp.custom_field_id = cf.id
				WHERE 		pm_wp.post_id = {$post_id} AND cf.name = pm.field_name AND cf.group_id=g.id AND
							pm_wp.meta_id=pm.id AND pm.field_name='$fieldName' AND pm.group_count = $groupIndex
							AND pm_wp.meta_value <> '' 
				ORDER BY 	pm.order_id, cf.display_order, pm.field_count";
			
		$data_fields = $wpdb->get_results($sql);
		MF_put_cached_data( $cache_name, serialize( $data_fields ) );
	}

	$info = null;
	foreach($data_fields as $data){
		switch($data->type){
			case $FIELD_TYPES["textbox"]:
			case $FIELD_TYPES["radiobutton_list"]:
			case $FIELD_TYPES["dropdown_list"]:
			case $FIELD_TYPES["color_picker"]:
			case $FIELD_TYPES["slider"]:
			case $FIELD_TYPES["related_type"]:
			case $FIELD_TYPES['markdown_textbox']:
				$info[$data->field_count] = $data->meta_value;
				break;
			case $FIELD_TYPES['multiline_textbox']:
				$info[$data->field_count] = $data->meta_value;
				break;
			case $FIELD_TYPES["checkbox"]: 		
					if ($data->meta_value == 'true')  $fieldValue = 1; else $fieldValue = 0;
					$info[$data->field_count] = $fieldValue; 
					break;
			case $FIELD_TYPES["checkbox_list"]:
			case $FIELD_TYPES["listbox"]:
					$info[$data->field_count] = unserialize($data->meta_value);
				break;
			case $FIELD_TYPES["audio"]:
			case $FIELD_TYPES["file"]:
				if ($data->meta_value != ""){ $fieldValue = MF_FILES_URI.$data->meta_value;}else{$fieldValue= null;}
				$info[$data->field_count] = $fieldValue;
				break;
			case $FIELD_TYPES['image']:
				if($data->meta_value != ""){
					$format = unserialize($data->properties);
					if($format) $info[$data->field_count]['t'] = aux_image($data->meta_value,$format['params']);
					$info[$data->field_count]['o'] = MF_FILES_URI.$data->meta_value;		
				}
				break;
			case $FIELD_TYPES['date']:
				$format = unserialize($data->properties);
				$fieldValue = GetProcessedFieldValue($data->meta_value, $data->type, $format);
				$info[$data->field_count] = $fieldValue;
				break;
	    case $FIELD_TYPES['Image (Upload Media)']:
  			if($data->meta_value != ""){
  				$format = unserialize($data->properties);
  				$image = wp_get_attachment_image_src($data->meta_value,'original');
  				if($format) $info[$data->order_id][$data->field_name][$data->field_count]['t'] = aux_image($image[0],$format['params'],$data->type);

    			$info[$data->order_id][$data->field_name][$data->field_count]['o'] = $image[0];		
    		}
    		break;
		}
	}
	return $info;
}





/* 
  Get a "set" of values, where a set is simply a group that is not able to be duplicated. This is a common way to related a 
  group of fields together in Magic Fields, and this function is an easier and faster way than "get" on each field individually.
*/


function get_set($name_group, $options = array(), $post_id = NULL) {
  
  $ret = array();

  $options = array_merge( array("flatten" => true, "prefix" => ""), (array) $options);

  $group = get_group($name_group, $post_id);
  
  if (count($group) > 0) {
    $single = $group[1];
    
    foreach ($single as $key=>$value) {
      $newkey = $key;
      
      if ($options["prefix"] != "") {
        $newkey = preg_replace("/^".preg_quote($options["prefix"])."/", "", $newkey);
      }
      
      if ($options["flatten"]) {
        $ret[$newkey] = $value[1];
      } else {
        $ret[$newkey] = $value;
      }
    }
  }

  return $ret; 
}



/* 
  
  Allows you to specify a few extra options for the get_group call:
  
  * flatten: field values will be assumed to have fields that have no duplicates, and the values of the fields will be stored directly against the keys, rather than against $key[1] 
  * prefix: this will be removed from the beginning of the key of each field in the group, to help readability of code in an example like that below:
  

  Suppose we a group name "People", with fields named "person_given_name", "person_family_name", "person_title"
  all of these fields are not duplicatable, but the group may be duplicated.
  (we add the person_ prefix to "namespace" the field, to ensure it is unique across all fields in the panel)
  
  Using get_group to iterate over them, code without a prefix, and might look like this:

  > $people = get_group("People");
  >  
  > foreach ($people as $person) {
  >   echo $person["person_title"][1]." ".$person["person_given_name"][1]." ".$person["person_family_name"][1];
  > }
  
  This is fine, but it's laborious to enter both the indexes, and the person_ prefix every time.
  
  Using get_group_with_options, with a prefix and flatten option, the code now looks like this:

  > $people = get_group_with_options("People", array("flatten" => true, $prefix => "people_"));
  >  
  > foreach ($people as $person) {
  >   echo $person["title"]." ".$person["given_name"]." ".$person["family_name"];
  > }

  Note that the get_group_with_options call is a little tricky itself, as array literals in PHP are not great compared to
  JavaScript so there are some alias functions under this one. The code above could be rewritten as:
  
  > $people = get_flat_group_with_prefix("People", "people_");
   
  > foreach ($people as $person) {
  >  echo $person["title"]." ".$person["given_name"]." ".$person["family_name"];
  > }

    

*/

function get_group_with_options($name_group, $options = array(), $post_id = NULL) {
  
  
  $options = array_merge( array("flatten" => false, "prefix" => ""), (array) $options);
  
  $ret = array();
  
  $group = get_group($name_group, $post_id);
  
  $count = 1;
  
  foreach ($group as $item) {
    $newitem = array();
    
    foreach ($item as $key => $value) {
      
      $newkey = $key;
      
      if ($options["prefix"] != "") {
          $newkey = preg_replace("/^".preg_quote($options["prefix"])."/", "", $newkey);
      }
      
      if ($options["flatten"]) { // fields with only 1 element will be set on the key directly, so we don't need to specify the [1] index in this common case 
        $newitem[$newkey] = $value[1];
      } else {
        $newitem[$newkey] = $value;
      } 

    }
    
    $ret[$count] = $newitem;
    $count++;
  }

  return $ret;
}


function get_group_with_prefix($name_group, $prefix, $post_id = NULL) {
  return get_group_with_options($name_group, array("prefix" => $prefix, "flatten" => FALSE), $post_id);
}

function get_flat_group($name_group, $post_id = NULL) {
  return get_group_with_options($name_group, array("flatten" => TRUE), $post_id);
}

function get_flat_group_with_prefix($name_group, $prefix, $post_id = NULL) {
  return get_group_with_options($name_group, array("prefix" => $prefix, "flatten" => TRUE), $post_id);
}

