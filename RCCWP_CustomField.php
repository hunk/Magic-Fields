<?php
/**
 *  In this Class  can be found it the methods for work with CustomFields.
 * 
 *  - Create a Custom Field
 *  - Delete a Field
 *  - Get a Custom Field
 *  - Get Info from the types of custom fields
 *  - Get the  postmeta ID  with the value of a custom field
 *  - Get a custom field Value
 */
class RCCWP_CustomField {
	/**
	 * Create a new custom field
	 *
	 * @param id $customGroupId the id of the group that will contain the field
	 * @param string $name the name of the field, the name is used to uniquely identify the field
	 * 							when retrieving its value.
	 * @param string $label the label of the field, the label is displayed beside the field
	 * 							in Write tab. 
	 * @param integer $order the order of the field when it is displayed in 
	 * 							the Write tab.
	 * @param integer $required_field whether this field is a required field. Required fields
	 * 							doesn't allow users to save a post if they are null. 
	 * 
	 * @param integer $type the type of the field. Use $FIELD_TYPES defined in MF_Constant.php
	 * @param array $options array of strings that represent the list of the field if
	 * 							its type is list.
	 * @param array $default_value array of strings that represent default value(s) of
	 * 							of the field if	its type is list.
	 * @param array $properties an array containing extra properties of the field.
	 * @return the new field id
	 */
	function Create($customGroupId, $name, $label, $order = 1, $required_field = 0, $type, $options = null, $default_value = null, $properties = null,$duplicate,$helptext = null,$css = null) {
		global $wpdb;
		$name = stripslashes(stripslashes($name));
		$name = addslashes($name);
		$name = str_replace(" ","_",$name);

		$label = stripslashes(stripslashes($label));
		$label = addslashes($label);
		
		$helptext = stripslashes(stripslashes($helptext));
		$helptext = addslashes($helptext);
    
		if(isset($_POST['custom-field-css'])) $css = $_POST['custom-field-css'];
		$sql = sprintf(
			"INSERT INTO " . MF_TABLE_GROUP_FIELDS .
			" (group_id, name, description, display_order, required_field, type, CSS, duplicate,help_text) values (%d, %s, %s, %d, %d, %d, %s, %d, %s)",
			$customGroupId,
			RC_Format::TextToSql($name),
			RC_Format::TextToSql($label),
			$order,
			$required_field,
			$type,
			"'".$css."'",
			$duplicate,
			RC_Format::TextToSql($helptext)
			);
		$wpdb->query($sql);
		
		$customFieldId = $wpdb->insert_id;
		
		$field_type = RCCWP_CustomField::GetCustomFieldTypes($type);
		if ($field_type->has_options == "true"){
			if (!is_array($options)) {
				$options = stripslashes($options);
				$options = explode("\n", $options);
			}

			array_walk($options, array("RC_Format", "TrimArrayValues"));
			$options = addslashes(serialize($options));
			
			if (!is_array($default_value)) {
				$default_value = stripslashes($default_value);
				$default_value = explode("\n", $default_value);
			}
			array_walk($default_value, array("RC_Format", "TrimArrayValues"));
			$default_value = addslashes(serialize($default_value));
			
			$sql = sprintf(
				"INSERT INTO " . MF_TABLE_CUSTOM_FIELD_OPTIONS .
				" (custom_field_id, options, default_option) values (%d, %s, %s)",
				$customFieldId,
				RC_Format::TextToSql($options),
				RC_Format::TextToSql($default_value)
				);	
			$wpdb->query($sql);	
		}
		
		if ($field_type->has_properties == "true"){
			$sql = sprintf(
				"INSERT INTO " . MF_TABLE_CUSTOM_FIELD_PROPERTIES .
				" (custom_field_id, properties) values (%d, %s)",
				$customFieldId,
				RC_Format::TextToSql(serialize($properties))
				);
			$wpdb->query($sql);
		}
		
		return $customFieldId;
	}
	
	/**
	 * Delete a field
	 *
	 * @param integer $customFieldId field id
	 */
	function Delete($customFieldId = null)
	{
		global $wpdb;
		
		$customField = RCCWP_CustomField::Get($customFieldId);
		
		$sql = sprintf(
			"DELETE FROM " . MF_TABLE_GROUP_FIELDS .
			" WHERE id = %d",
			$customFieldId
			);
		$wpdb->query($sql);
		
		if ($customField->has_options == "true")
		{
			$sql = sprintf(
				"DELETE FROM " . MF_TABLE_CUSTOM_FIELD_OPTIONS .
				" WHERE custom_field_id = %d",
				$customFieldId
				);	
			$wpdb->query($sql);	
		}
	}
	
	/**
	 * Get the field information including properties, options and default value(s)
	 *
	 * @param integer $customFieldId field id
	 * @return an object containing information about fields. The object contains 
	 * 			3 objects: properties, options and default_value
	 */
	function Get($customFieldId) {
		global $wpdb,$mf_field_types;
		$sql = "SELECT cf.group_id, cf.id, cf.name, cf.CSS,cf.type as custom_field_type, cf.description, cf.display_order, cf.required_field, co.options, co.default_option AS default_value, cp.properties,duplicate,cf.help_text FROM " . MF_TABLE_GROUP_FIELDS .
			" cf LEFT JOIN " . MF_TABLE_CUSTOM_FIELD_OPTIONS . " co ON cf.id = co.custom_field_id" .
			" LEFT JOIN " . MF_TABLE_CUSTOM_FIELD_PROPERTIES . " cp ON cf.id = cp.custom_field_id" .
			" WHERE cf.id = " . $customFieldId;

		$results = $wpdb->get_row($sql);

		$results->type 					= $mf_field_types[$results->custom_field_type]['name'];
		$results->type_id 				= $results->custom_field_type;
		$results->has_options 			= $mf_field_types[$results->custom_field_type]['has_options'];
		$results->has_properties 		= $mf_field_types[$results->custom_field_type]['has_properties'];
		$results->allow_multiple_values = $mf_field_types[$results->custom_field_type]['allow_multiple_values'];
		
		$results->options = unserialize($results->options);
		$results->properties = unserialize($results->properties);
		$results->default_value = unserialize($results->default_value);

		return $results;
	}
	
	/**
	 * Retrieves information about a specified type
	 *
	 * @param integer $customFieldTypeId the type id, if null, a list of all types will be returned
	 * @todo This option should be deprecated
	 * @return a list/object containing information about the specified type. The information 
	 * 			includes id, name, description, has_options, has_properties, and
	 * 			allow_multiple_values (whether fields of that type can have more than one default value)
	 */
	function GetCustomFieldTypes($customFieldTypeId = null) {
		global $wpdb,$mf_field_types;
	
		if (isset($customFieldTypeId) && is_numeric($customFieldTypeId)){
			$results = (object)$mf_field_types[$customFieldTypeId];
		}else{
			foreach($mf_field_types as $type){
				$results[] = (object)$type;
			}
		}
		return $results;
	}
	
	/**
	 *  Get the Meta ID from a custom field with this id is possible get the value of the custom field
	 *  from the Post Meta table of wordpress
	 * 
	 *  @param  integer $postId   Post id
	 *	@param  string  $customFieldNamethe name of the custom field
	 *  @param 	integer $groupIndex the index of the group (if the field is content into a group)
	 *  @param 	integer $fieldIndex  the index of the field
	 *  @return integer Return the id from the postmeta table of wordpress
	 * 					 who  contain the value of the custom field
	 */
	function GetMetaID($postId, $customFieldName, $groupIndex=1, $fieldIndex=1) {
		global $wpdb;
		
		// Given $postId, $customFieldName, $groupIndex and $fieldIndex get meta_id
		return $wpdb->get_var("SELECT id FROM " . MF_TABLE_POST_META . 
						" WHERE field_name = '$customFieldName' AND group_count = $groupIndex ". 
						" AND field_count = $fieldIndex AND post_id = $postId" );
		
	}
	
	/**
	 * Retrieves the value of a custom field for a specified post
	 *
	 * @param boolean $single
	 * @param integer $postId
	 * @param string $customFieldName
	 * @param integer $groupIndex
	 * @param integer $fieldIndex
	 * @return mixed
	 * @TODO review if is still necessary save the "backward compatibility"
	 */
	function GetCustomFieldValues($single, $postId, $customFieldName, $groupIndex=1, $fieldIndex=1) {
		global $wpdb;
		$customFieldName = str_replace(" ","_",$customFieldName);
		$fieldMetaID = RCCWP_CustomField::GetMetaID($postId, $customFieldName, $groupIndex, $fieldIndex);
		
		// for backward compatability, if no associated row was found, use old method
		if (!$fieldMetaID){
			return get_post_meta($postId, $customFieldName, $single);
		}

		// Get meta value
		$mid = (int) $fieldMetaID;
		$meta = $wpdb->get_row( "SELECT * FROM $wpdb->postmeta WHERE meta_id = '$mid'" );
		if (!$single) return unserialize($meta->meta_value);
		return $meta->meta_value;
	}
	
	/**
	 * Get number of group duplicates given field name. The function returns 1
 	 * if there are no duplicates (just the original group), 2 if there is one
 	 * duplicate and so on.
	 *
	 * @param integer $postId post id
	 * @param integer $fieldID the name of any field in the group
	 * @return number of groups 
	 */
	function GetFieldGroupDuplicates($postId, $fieldName){
		global $wpdb;
		return $wpdb->get_var("SELECT count(DISTINCT group_count) FROM " . MF_TABLE_POST_META . 
						" WHERE field_name = '$fieldName' AND post_id = $postId");
	}

	/**
	 * Get number of group duplicates given field name. The function returns 1
 	 * if there are no duplicates (just he original group), 2 if there is one
 	 * duplicate and so on.
	 *
	 * @param integer $postId post id
	 * @param integer $fieldID the name of any field in the group
	 * @return number of groups 
	 */
	function GetFieldDuplicates($postId, $fieldName, $groupIndex){
		global $wpdb;

		return $wpdb->get_var("SELECT count(DISTINCT field_count) FROM " . MF_TABLE_POST_META . 
						" WHERE field_name = '$fieldName' AND post_id = $postId AND group_count = $groupIndex");
	}

	/**
	* Get field duplicates
	*  
	*  @param $postId   the id of the post
	*  @param $fieldName the name of the field
	*  @param $groupId the groupId  
	*  @return  array  return the order of the field sorted
	*/ 
	function GetFieldsOrder($postId,$fieldName,$groupId){
		global $wpdb;

		$tmp =  $wpdb->get_col("SELECT field_count FROM ".MF_TABLE_POST_META." WHERE field_name = '{$fieldName}' AND post_id = {$postId} AND group_count = {$groupId} GROUP BY field_count ORDER BY field_count ASC");

		// if the array is  empty is because this field is new and don't have
		// a data related with this post 
		// then we just create with the index 1
		if(empty($tmp)){
			$tmp[0] = 1;
		}

		return $tmp;
	}

	/**
	 * Get the order of group duplicates given the  field name. The function returns a
	 * array  with the orden  
	 *
	 * @param integer  $postId post id
	 * @param integer $fieldID  the name of any field in the group
	 * @return order of one group
	 */
	function GetOrderDuplicates($postId,$fieldName){
		global $wpdb;

		$tmp =  $wpdb->get_col("SELECT group_count  FROM ".MF_TABLE_POST_META." WHERE field_name = '{$fieldName}' AND   post_id = {$postId} GROUP BY group_count ORDER BY order_id asc");

		// if the array is  empty is because this field is new and don't have
		// a data related with this post 
		// then we just create with the index 1
		if(empty($tmp)){
			$tmp[0] = 1;
		}

		 
		// the order start to 1  and the arrays start to 0
		// then i just sum one element in each array key for 
		// the  order and the array keys  be the same
		$order  = array();
		foreach($tmp as $key => $value){
			$order[$key+1]  = $value;
		} 
		return $order;

	}
	
	/**
	 * Retrieves the id and type of a custom field given field name for the current post.
	 *
	 * @param string $customFieldName
	 * @return array with custom field id and custom field type
	 * @author Edgar García - hunk  <ing.edgar@gmail.com>
	 */
	function GetInfoByName($customFieldName,$post_id){
		global $wpdb, $FIELD_TYPES;
		
		$customFieldvalues = $wpdb->get_row(
			"SELECT cf.id, cf.type,cf.CSS,fp.properties,cf.description 
				FROM ". MF_TABLE_GROUP_FIELDS . " cf 
					LEFT JOIN ".MF_TABLE_CUSTOM_FIELD_PROPERTIES." fp ON fp.custom_field_id = cf.id
					WHERE cf.name = '$customFieldName' 
						AND cf.group_id in (
							SELECT mg.id 
								FROM ". MF_TABLE_PANEL_GROUPS . " mg, ".$wpdb->postmeta." pm 
									WHERE mg.panel_id = pm.meta_value
									AND pm.meta_key = '".RC_CWP_POST_WRITE_PANEL_ID_META_KEY."' 
									AND pm.post_id = $post_id)",ARRAY_A);
													
		if (empty($customFieldvalues)) 
			return false;

		if($customFieldvalues['type'] == $FIELD_TYPES["date"] OR $customFieldvalues['type'] == $FIELD_TYPES["image"] )
			$customFieldvalues['properties'] = unserialize($customFieldvalues['properties']);
		else 
			$customFieldvalues['properties']=null;
		
		return $customFieldvalues;
	}


	/**
	 * Updates the properties of a custom field.
	 *
	 * @param integer $customFieldId the id of the field to be updated
	 * @param string $name the name of the field, the name is used to uniquely identify the field
	 * 							when retrieving its value.
	 * @param string $label the label of the field, the label is displayed beside the field
	 * 							in Write tab. 
	 * @param integer $order the order of the field when it is displayed in 
	 * 							the Write tab.
	 * @param integer $required_field whether this field is a required field. Required fields
	 * 							doesn't allow users to save a post if they are null. 
	 * @param integer $type the type of the field. Use $FIELD_TYPES defined in MF_Constant.php
	 * @param array $options array of strings that represent the list of the field if
	 * 							its type is list.
	 * @param array $default_value array of strings that represent default value(s) of
	 * 							of the field if	its type is list.
	 * @param array $properties an array containing extra properties of the field.
	 */

	function Update($customFieldId, $name, $label, $order = 1, $required_field = 0, $type, $options = null, $default_value = null, $properties = null, $duplicate,$helptext = null) {
		global $wpdb;
		$name = str_replace(" ","_",$name);
		$oldCustomField = RCCWP_CustomField::Get($customFieldId);
		
		if ($oldCustomField->name != $name)
		{
			$sql = sprintf(
				"UPDATE $wpdb->postmeta" .
				" SET meta_key = %s" .
				" WHERE meta_key = %s",
				RC_Format::TextToSql($name),
				RC_Format::TextToSql($oldCustomField->name)
				);
			
			$wpdb->query($sql);
		}
		$css = NULL;
		if(isset($_POST['custom-field-css'])) $css = $_POST['custom-field-css'];
		$sql = sprintf(
			"UPDATE " . MF_TABLE_GROUP_FIELDS .
			" SET name = %s" .
			" , description = %s" .
			" , display_order = %d" .
			" , required_field = %d" .
			" , type = %d" .
			" , CSS = '%s'" .
			" , duplicate = %d" .
			" , help_text = %s" .
			" WHERE id = %d",
			RC_Format::TextToSql($name),
			RC_Format::TextToSql($label),
			$order,
			$required_field,
			$type,
			$css,
			$duplicate,
			RC_Format::TextToSql($helptext),
			$customFieldId
			);
		$wpdb->query($sql);


		$field_type = RCCWP_CustomField::GetCustomFieldTypes($type);
		if ($field_type->has_options == "true")
		{
			if (!is_array($options)) {
				$options = stripslashes($options);
				$options = explode("\n", $options);
			}
			array_walk($options, array("RC_Format", "TrimArrayValues"));
			$options = addslashes(serialize($options));
			
			if (!is_array($default_value)) {
				$default_value = stripslashes($default_value);
				$default_value = explode("\n", $default_value);
			}
			array_walk($default_value, array("RC_Format", "TrimArrayValues"));
			$default_value = addslashes(serialize($default_value));
			
			$sql = sprintf(
				"INSERT INTO " . MF_TABLE_CUSTOM_FIELD_OPTIONS .
				" (custom_field_id, options, default_option) values (%d, %s, %s)" . 
				" ON DUPLICATE KEY UPDATE options = %s, default_option = %s",
				$customFieldId,
				RC_Format::TextToSql($options),
				RC_Format::TextToSql($default_value),
				RC_Format::TextToSql($options),
				RC_Format::TextToSql($default_value)
				);	
			$wpdb->query($sql);	
		}
		else
		{
			$sql = sprintf(
				"DELETE FROM " . MF_TABLE_CUSTOM_FIELD_OPTIONS .
				" WHERE custom_field_id = %d",
				$customFieldId
				);
			$wpdb->query($sql);	
		}
		
		if ($field_type->has_properties == "true")
		{
			$sql = sprintf(
				"INSERT INTO " . MF_TABLE_CUSTOM_FIELD_PROPERTIES .
				" (custom_field_id, properties) values (%d, %s)" .
				" ON DUPLICATE KEY UPDATE properties = %s",
				$customFieldId,
				RC_Format::TextToSql(serialize($properties)),
				RC_Format::TextToSql(serialize($properties))
				);	
			$wpdb->query($sql);	
		}
		else
		{
			$sql = sprintf(
				"DELETE FROM " . MF_TABLE_CUSTOM_FIELD_PROPERTIES .
				" WHERE custom_field_id = %d",
				$customFieldId
				);
			$wpdb->query($sql);	
		}
	}
	
	
	/**
	 *  Get Data Field
	 *  @param  string $customFieldName
	 *  @param  integer $groupIndex
	 *  @param 	integer $fieldIndex
	 *  @param 	integer	$postId
	 */
	function GetDataField($customFieldName, $groupIndex=1, $fieldIndex=1,$postId){
		global $wpdb, $FIELD_TYPES;
		$customFieldName = str_replace(" ","_",$customFieldName);
		
		$customFieldvalues = $wpdb->get_row(
			"SELECT pm.meta_id,pm.meta_value, cf.id, cf.type,cf.CSS,fp.properties,cf.description 
			FROM ".MF_TABLE_POST_META." pm_mf, ".$wpdb->postmeta." pm, ".MF_TABLE_GROUP_FIELDS." cf LEFT JOIN ".MF_TABLE_CUSTOM_FIELD_PROPERTIES." fp ON fp.custom_field_id = cf.id 
			WHERE cf.name = '$customFieldName' AND cf.name = pm_mf.field_name AND group_count = $groupIndex AND field_count = $fieldIndex AND pm_mf.post_id= $postId AND pm_mf.id = pm.meta_id AND ( cf.group_id in ( SELECT mg.id FROM ".MF_TABLE_PANEL_GROUPS." mg, ".$wpdb->postmeta." pm WHERE mg.panel_id = pm.meta_value AND pm.meta_key = '_mf_write_panel_id' AND pm.post_id = $postId)
      OR cf.group_id IN (SELECT mg.id FROM ".MF_TABLE_PANEL_GROUPS." mg INNER JOIN ".MF_TABLE_PANELS." mfwp ON mg.panel_id = mfwp.id  WHERE mfwp.name = '_Global'))
			",ARRAY_A);

    // traversal addition to the query above to support the global panel
    
		if (empty($customFieldvalues)) 
			return null;
		
		$customFieldvalues['properties'] = unserialize($customFieldvalues['properties']);
		
		if($customFieldvalues['type'] == $FIELD_TYPES["checkbox_list"] OR $customFieldvalues['type'] == $FIELD_TYPES["listbox"] )
			$customFieldvalues['meta_value'] = unserialize($customFieldvalues['meta_value']);
								
		return $customFieldvalues;
	}
}
