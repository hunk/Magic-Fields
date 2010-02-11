<?php

function mf_shortcodes($atts) {
    global $post, $FIELD_TYPES;
	extract(shortcode_atts(array(
		'field' => 'no field defined or field name is wrong',
		'eip' => FALSE,
		'filtered' => FALSE,
		'imgtag' => FALSE,
		'label' => "",
		'loop' => FALSE,
		'loopseparator' => "|",
		'checked' => "yes",
		'groupindex' => 1,
		'fieldindex' => 1,
	), $atts));
	if ($loop && (RCCWP_CustomField::GetFieldDuplicates($post->ID,$field,$groupindex)>1)) {
	    $fieldduplicatedata = get_field_duplicate($field);
	} else {
            $fielddata = RCCWP_CustomField::GetDataField($field,$groupindex,$fieldindex,$post->ID);
	}
	$fieldType = $fielddata['type'];
	$fieldID = $fielddata['id'];
	$fieldObject = $fielddata['properties'];
	$fieldValues = (array)$fielddata['meta_value'];
	$fieldMetaID = $fielddata['meta_id'];

	$fieldresults = GetProcessedFieldValue($fieldValues, $fieldType, $fieldObject);
	$shortcode_data = $fieldresults;
	if(($fielddata['type'] == $FIELD_TYPES['multiline_textbox']) && $filtered){
		$shortcode_data = apply_filters('the_content', $fieldresults);
	}
	if($fielddata['type'] == $FIELD_TYPES['image']){
		$imgresults = split('&',$fieldresults);
		if ($imgtag) {
		    $shortcode_data = "<img src=\"$imgresults[0]\"/>";
		} else {
		$shortcode_data = $imgresults[0];
		}
	}
	if($fielddata['type'] == $FIELD_TYPES['listbox']){
		$shortcode_data = implode(",",$fieldresults);
	}
	if($fielddata['type'] == $FIELD_TYPES['checkbox_list']){
		$shortcode_data = implode(",",$fieldresults);
	}
	if($fielddata['type'] == $FIELD_TYPES['checkbox']){
	    if($fieldresults)
		$shortcode_data = $checked;
	}
	// Prepare fields for EIP
	$enableEditnplace = RCCWP_Options::Get('enable-editnplace');
	if ($eip && $enableEditnplace == 1 && current_user_can('edit_posts', $post->ID)){
		switch($fielddata['type']){
			case $FIELD_TYPES["textbox"]:
				if(!$results) $results="&nbsp";
				$shortcode_data = "<div class='".EIP_textbox($fieldMetaID)."' >".$fieldresults."</div>";
				break;
			case $FIELD_TYPES["multiline_textbox"]:
				if(!$results) $results="&nbsp";
				$shortcode_data = "<div class='".EIP_mulittextbox($fieldMetaID)."' >".$shortcode_data."</div>";
				break;
		}
	}
    if ($shortcode_data || $fieldduplicatedata) {
    if ($label) {
	$shortcode_data = $label.$shortcode_data;
    } else {
	$shortcode_data = get_label($field)." : ".$shortcode_data;
    }
    /////
    if ($field == "duplicate") {
	if ($loop) {
	    return implode($loopseparator,$fieldduplicatedata);
	} else {
	    return $shortcode_data;
        }
    } else {
	return $shortcode_data;
    }
    /////
    } else {
        return "no data found, please check the field name";
    }
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
