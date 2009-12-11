<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// [mf field="foo-value" loop=true]
function mf_shortcodes($atts) {
    global $post, $FIELD_TYPES;
//    require_once("../RCCWP_CustomField.php");
	extract(shortcode_atts(array(
		'field' => 'no field defined or field name is wrong',
		'eip' => FALSE,
		'filtered' => FALSE,
		'imgtag' => FALSE,
		'label' => "",
		'checked' => "yes",
	), $atts));
        $fielddata = RCCWP_CustomField::GetDataField($field,1,1,$post->ID);
	$fieldType = $fielddata['type'];
	$fieldID = $fielddata['id'];
	$fieldObject = $fielddata['properties'];
	$fieldValues = (array)$fielddata['meta_value'];
	$fieldMetaID = $fielddata['meta_id'];

	$results = GetProcessedFieldValue($fieldValues, $fieldType, $fieldObject);
	$shortcode_result = $results;
	if(($fielddata['type'] == $FIELD_TYPES['multiline_textbox']) && $filtered){
		$shortcode_result = apply_filters('the_content', $results);
	}
	if($fielddata['type'] == $FIELD_TYPES['image']){
		$results = split('&',$results);
		if ($imgtag) {
		    $shortcode_result = "<img src=\"$results[0]\"/>";
		} else {
		$shortcode_result = $results[0];
		}
	}
	if($fielddata['type'] == $FIELD_TYPES['listbox']){
		$shortcode_result = implode(",",$results);
	}
	if($fielddata['type'] == $FIELD_TYPES['checkbox_list']){
		$shortcode_result = implode(",",$results);
	}
	if($fielddata['type'] == $FIELD_TYPES['checkbox']){
	    if($results)
		$shortcode_result = $checked;
	}
	// Prepare fields for EIP
	$enableEditnplace = RCCWP_Options::Get('enable-editnplace');
	if ($eip && $enableEditnplace == 1 && current_user_can('edit_posts', $post->ID)){
		switch($fielddata['type']){
			case $FIELD_TYPES["textbox"]:
				if(!$results) $results="&nbsp";
				$shortcode_result = "<div class='".EIP_textbox($fieldMetaID)."' >".$results."</div>";
				break;
			case $FIELD_TYPES["multiline_textbox"]:
				if(!$results) $results="&nbsp";
				$shortcode_result = "<div class='".EIP_mulittextbox($fieldMetaID)."' >".$shortcode_result."</div>";
				break;
		}
	}

    if ($label) {
	$shortcode_result = $label.$shortcode_result;
    } else {
	$shortcode_result = get_label($field)." : ".$shortcode_result;
    }
    return $shortcode_result;
}
add_shortcode('mf', 'mf_shortcodes');

// [bartag foo="foo-value"]
function bartag_func($atts) {
	extract(shortcode_atts(array(
		'foo' => 'no foo',
		'bar' => 'default bar',
	), $atts));

	return "foo = {$foo}";
}
add_shortcode('bartag', 'bartag_func');

?>
