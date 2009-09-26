<?php

class RCCWP_CustomField
{
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
	 * @param integer $type the type of the field. Use $FIELD_TYPES defined in RCCWP_Constant.php
	 * @param array $options array of strings that represent the list of the field if
	 * 							its type is list.
	 * @param array $default_value array of strings that represent default value(s) of
	 * 							of the field if	its type is list.
	 * @param array $properties an array containing extra properties of the field.
	 * @return the new field id
	 */
	function Create($customGroupId, $name, $label, $order = 1, $required_field = 0, $type, $options = null, $default_value = null, $properties = null,$duplicate)
	{
		global $wpdb;

		$name = stripslashes(stripslashes($name));
		$name = addslashes($name);
		$name = str_replace(" ","_",$name);

		$label = stripslashes(stripslashes($label));
		$label = addslashes($label);

		$sql = sprintf(
			"INSERT INTO " . MF_TABLE_GROUP_FIELDS .
			" (group_id, name, description, display_order, required_field, type, CSS, duplicate) values (%d, %s, %s, %d, %d, %d, %s, %d)",
			$customGroupId,
			RC_Format::TextToSql($name),
			RC_Format::TextToSql($label),
			$order,
			$required_field,
			$type,
			"'".$_POST['custom-field-css']."'",
			$duplicate
			);
		$wpdb->query($sql);
		
		$customFieldId = $wpdb->insert_id;
		
		$field_type = RCCWP_CustomField::GetCustomFieldTypes($type);
		if ($field_type->has_options == "true")
		{
			if (!is_array($options)) {
				$options = stripslashes($options);
				$options = explode("\n", $options);
			}
			array_walk($options, array(RC_Format, TrimArrayValues));
			$options = addslashes(serialize($options));
			
			if (!is_array($default_value)) {
				$default_value = stripslashes($default_value);
				$default_value = explode("\n", $default_value);
			}
			array_walk($default_value, array(RC_Format, TrimArrayValues));
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
		
		if ($field_type->has_properties == "true")
		{
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
	function Get($customFieldId)
	{
		global $wpdb;
		$sql = "SELECT cf.group_id, cf.id, cf.name, cf.CSS, tt.id AS type_id, tt.name AS type, cf.description, cf.display_order, cf.required_field, co.options, co.default_option AS default_value, tt.has_options, cp.properties, tt.has_properties, tt.allow_multiple_values, duplicate FROM " . MF_TABLE_GROUP_FIELDS .
			" cf LEFT JOIN " . MF_TABLE_CUSTOM_FIELD_OPTIONS . " co ON cf.id = co.custom_field_id" .
			" LEFT JOIN " . MF_TABLE_CUSTOM_FIELD_PROPERTIES . " cp ON cf.id = cp.custom_field_id" .
			" JOIN " . MF_TABLE_CUSTOM_FIELD_TYPES . " tt ON cf.type = tt.id" . 
			" WHERE cf.id = " . $customFieldId;
		$results = $wpdb->get_row($sql);
			
		$results->options = unserialize($results->options);
		$results->properties = unserialize($results->properties);
		$results->default_value = unserialize($results->default_value);
		return $results;
	}
	
	/**
	 * Retrievies information about a specified type
	 *
	 * @param integer $customFieldTypeId the type id, if null, a list of all types will be returned
	 * @return a list/object containing information about the specified type. The information 
	 * 			includes id, name, description, has_options, has_properties, and
	 * 			allow_multiple_values (whether fields of that type can have more than one default value)
	 */
	function GetCustomFieldTypes($customFieldTypeId = null)
	{
		global $wpdb;
	
		if (isset($customFieldTypeId))
		{
			$sql = "SELECT id, name, description, has_options, has_properties, allow_multiple_values FROM " . MF_TABLE_CUSTOM_FIELD_TYPES .
				" WHERE id = " . (int)$customFieldTypeId;
			$results = $wpdb->get_row($sql);	
		}
		else
		{
			$sql = "SELECT id, name, description, has_options, has_properties, allow_multiple_values FROM " . MF_TABLE_CUSTOM_FIELD_TYPES;
			$results = $wpdb->get_results($sql);
			if (!isset($results))
				$results = array();
		}
		return $results;
	}
	
	function GetMetaID($postId, $customFieldName, $groupIndex=1, $fieldIndex=1){
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
	 * @return a
	 */
	function GetCustomFieldValues($single, $postId, $customFieldName, $groupIndex=1, $fieldIndex=1)
	{
		global $wpdb;
		$customFieldName = str_replace(" ","_",$customFieldName);
		$fieldMetaID = RCCWP_CustomField::GetMetaID($postId, $customFieldName, $groupIndex, $fieldIndex);
		
		// for backward compatability, if no accociated row was found, use old method
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
	 * Retrieves the value of a custom field for a specified post
	 *
	 * @param boolean $single
	 * @param integer $postId
	 * @param string $customFieldName
	 * @param integer $groupIndex
	 * @param integer $fieldIndex
	 * @return int|string Value of the custom field
	 * @author Edgar García - hunk <ing.edgar@gmail.com>
	 */
	function GetValues($single, $postId, $customFieldName, $groupIndex=1, $fieldIndex=1)
	{
		global $wpdb;
		$customFieldName = str_replace(" ","_",$customFieldName);
		
		$meta = $wpdb->get_var("SELECT pm.meta_value 
								FROM ".MF_TABLE_POST_META." mf_pm, ".$wpdb->postmeta." pm 
								WHERE mf_pm.field_name = '$customFieldName' 
									AND mf_pm.group_count = $groupIndex 
									AND mf_pm.field_count = $fieldIndex 
									AND mf_pm.post_id = $postId 
									AND mf_pm.id = pm.meta_id" );
		
		if(!$meta) return;
		if (!$single) return unserialize($meta);
		return $meta;
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
    *
    */ 
    function GetFieldsOrder($postId,$fieldName,$groupId){
        global $wpdb;

        $tmp =  $wpdb->get_col(
                                "SELECT field_count FROM ".MF_TABLE_POST_META." WHERE field_name = '{$fieldName}' AND post_id = {$postId} AND group_count = {$groupId} GROUP BY field_count ORDER BY field_count ASC"
                              );


        //if the array is  empty is because this field is new and don't have
        //a data related with this post 
        //then we just create with the index 1
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

        
         $tmp =  $wpdb->get_col(
                                   "SELECT group_count  FROM ".MF_TABLE_POST_META." WHERE field_name = '{$fieldName}' AND   post_id = {$postId} GROUP BY group_count ORDER BY order_id asc"
                                 );



         //if the array is  empty is because this field is new and don't have
         //a data related with this post 
         //then we just create with the index 1
         if(empty($tmp)){
             $tmp[0] = 1;
         }

         
         //the order start to 1  and the arrays start to 0
         //then i just sum one element in each array key for 
         //the  order and the array keys  be the same
         $order  = array();
         foreach($tmp as $key => $value){
            $order[$key+1]  = $value;
         } 
         return $order;

     }

	
	/**
	 * Retrieves the id of a custom field given field name for the current post.
	 *
	 * @param string $customFieldName
	 * @return custom field id
	 */
	function GetIDByName($customFieldName)
	{
		global $wpdb, $post;
		
		// Get Panel ID
		$customWritePanelId = get_post_meta($post->ID, RC_CWP_POST_WRITE_PANEL_ID_META_KEY, true);
		
		if (empty($customWritePanelId)) return false;
		
		$customFieldId = $wpdb->get_var("SELECT cf.id FROM ". MF_TABLE_GROUP_FIELDS . " cf" .
										" WHERE cf.name = '$customFieldName' AND ".
										" cf.group_id in (SELECT mg.id FROM ". MF_TABLE_PANEL_GROUPS . " mg ".
														"  WHERE mg.panel_id = $customWritePanelId)");
														
														
		return $customFieldId;
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
			"SELECT cf.id, cf.type,cf.CSS,fp.properties 
				FROM ". MF_TABLE_GROUP_FIELDS . " cf 
					LEFT JOIN ".MF_TABLE_CUSTOM_FIELD_PROPERTIES." fp ON fp.custom_field_id = cf.id
					WHERE cf.name = '$customFieldName' 
						AND cf.group_id in (
							SELECT mg.id 
								FROM ". MF_TABLE_PANEL_GROUPS . " mg, ".$wpdb->postmeta." pm 
									WHERE mg.panel_id = pm.meta_value
									AND pm.meta_key = '".RC_CWP_POST_WRITE_PANEL_ID_META_KEY."' 
									AND pm.post_id = $post_id)",ARRAY_A);
													
		if (empty($customFieldvalues)) return false;
		if($customFieldvalues['type'] == $FIELD_TYPES["date"] OR $customFieldvalues['type'] == $FIELD_TYPES["image"] )
			$customFieldvalues['properties'] = unserialize($customFieldvalues['properties']);
		else $customFieldvalues['properties']=null;
										
		return $customFieldvalues;
	}
	
	
	/**
	 * @access private 
	 */
	function GetDefaultCustomFieldType()
	{
		return 'Textbox';
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
	 * @param integer $type the type of the field. Use $FIELD_TYPES defined in RCCWP_Constant.php
	 * @param array $options array of strings that represent the list of the field if
	 * 							its type is list.
	 * @param array $default_value array of strings that represent default value(s) of
	 * 							of the field if	its type is list.
	 * @param array $properties an array containing extra properties of the field.
	 */

	function Update($customFieldId, $name, $label, $order = 1, $required_field = 0, $type, $options = null, $default_value = null, $properties = null, $duplicate)
	{
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
		
		$sql = sprintf(
			"UPDATE " . MF_TABLE_GROUP_FIELDS .
			" SET name = %s" .
			" , description = %s" .
			" , display_order = %d" .
			" , required_field = %d" .
			" , type = %d" .
			" , CSS = '%s'" .
			" , duplicate = %d" .
			" WHERE id = %d",
			RC_Format::TextToSql($name),
			RC_Format::TextToSql($label),
			$order,
			$required_field,
			$type,
			$_POST['custom-field-css'],
			$duplicate,
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
			array_walk($options, array(RC_Format, TrimArrayValues));
			$options = addslashes(serialize($options));
			
			if (!is_array($default_value)) {
				$default_value = stripslashes($default_value);
				$default_value = explode("\n", $default_value);
			}
			array_walk($default_value, array(RC_Format, TrimArrayValues));
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
}
?>