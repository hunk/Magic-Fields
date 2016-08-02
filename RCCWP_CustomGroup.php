<?php
/**
 *  In this Class  can be found it the methods for work with Groups.
 * 
 *  - Create a Group
 *  - Delete a Group 
 *  - Get a Group
 *  - Know if the group is empty or has at least one custom field
 *  - Update a Group
 *  - Get the custom fields of a group
 */
class RCCWP_CustomGroup {
	
	/**
	 * Create a new group in a write panel
	 *
	 * @param unknown_type $customWritePanelId
	 * @param unknown_type $name group name
	 * @param unknown_type $duplicate a boolean indicating whether the group can be duplicated
	 * @param unknown_type $at_right a boolean indicating whether the group should be placed at right side.
	 * @return the id of the new group
	 */
	public static function Create($customWritePanelId, $name, $duplicate, $expanded = 1, $at_right = 0) {
		require_once('RC_Format.php');
		global $wpdb;

		$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

		$wpdb->insert( 
			MF_TABLE_PANEL_GROUPS, 
			array( 
				'panel_id' => $customWritePanelId, 
				'name' => $name,
				'duplicate' => $duplicate,
				'expanded' => $expanded,
				'at_right' => $at_right
			), 
			array( 
				'%d',
				'%s',
				'%d',
				'%d',
				'%d'
			) 
		);

		$customGroupId = $wpdb->insert_id;
		return $customGroupId;
	}
	
	/**
	 * Delete a group given id
	 *
	 * @param integer $customGroupId
	 */
	public static function Delete($customGroupId = null) {
		include_once ('RCCWP_CustomField.php');
		if (isset($customGroupId)) {
			global $wpdb;
			
			$customFields = RCCWP_CustomGroup::GetCustomFields($customGroupId);
			foreach ($customFields as $field)  {
				RCCWP_CustomField::Delete($field->id);
  			}

			$wpdb->delete( MF_TABLE_PANEL_GROUPS, array( 'id' => $customGroupId ), array( '%d' ) );
		}
	}
	
	/**
	 * Get group properties
	 *
	 * @param integer $groupId
	 * @return an object representing the group
	 */
	
	public static function Get($groupId) {
		global $wpdb;
		
		$sql = $wpdb->prepare( "SELECT * FROM " .MF_TABLE_PANEL_GROUPS. " WHERE id = %d", array( $groupId ) );
		$results = $wpdb->get_row($sql);
		return $results;
	}
	
	/**
	 *  Has custom fields the group?
	 *	@param interger $fcustomGroupId the group id
	 *  @return bool return true if the group has at least one filed false if is empty
	 */
	public static function HasCustomfields($customGroupId){
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT  count(*) FROM ".MF_TABLE_GROUP_FIELDS." WHERE group_id = %d",$customGroupId);
		$results = $wpdb->get_var($sql);
		return  $results > 0;
	}
	
	/**
	 * Get a list of the custom fields of a group
	 *
	 * @param integer $customGroupId the group id
	 * @return an array of objects containing information about fields. Each object contains 
	 * 			3 objects: properties, options and default_value   
	 */
	public static function GetCustomFields($customGroupId) {
		global $wpdb,$mf_field_types;

		$sql = $wpdb->prepare( "SELECT cf.id,cf.type as custom_field_type, cf.name,cf.description, cf.display_order, cf.required_field,cf.css, co.options, co.default_option AS default_value,cp.properties,cf.duplicate,cf.help_text FROM " . MF_TABLE_GROUP_FIELDS .
			" cf LEFT JOIN " . MF_TABLE_CUSTOM_FIELD_OPTIONS . " co ON cf.id = co.custom_field_id" .
			" LEFT JOIN " . MF_TABLE_CUSTOM_FIELD_PROPERTIES . " cp ON cf.id = cp.custom_field_id" .
			" WHERE group_id = %d " .
			" ORDER BY cf.display_order,cf.id ASC", array( $customGroupId ) );    
		$results =$wpdb->get_results($sql);
		if (!isset($results))
			$results = array();
		
		for ($i = 0; $i < $wpdb->num_rows; ++$i) {
			$results[$i]->type 					= $mf_field_types[$results[$i]->custom_field_type]['name'];
			$results[$i]->type_id				= $results[$i]->custom_field_type;
			$results[$i]->has_options 			= $mf_field_types[$results[$i]->custom_field_type]['has_options'];
			$results[$i]->has_properties 		= $mf_field_types[$results[$i]->custom_field_type]['has_properties'];
			$results[$i]->allow_multiple_values = $mf_field_types[$results[$i]->custom_field_type]['allow_multiple_values'];

			$results[$i]->options = unserialize(stripslashes($results[$i]->options));
			$results[$i]->properties = unserialize($results[$i]->properties);
			$results[$i]->default_value = unserialize($results[$i]->default_value);
		}
	
		return $results;
	}
	
	/**
	 * Update the group
	 *
	 * @param unknown_type $customWritePanelId
	 * @param unknown_type $name group name
	 * @param unknown_type $duplicate a boolean indicating whether the group can be duplicated
	 * @param unknown_type $at_right a boolean indicating whether the group should be placed at right side. 
	 */	
	public static function Update($customGroupId, $name, $duplicate, $expanded, $at_right) {
		require_once('RC_Format.php');
		global $wpdb;

		$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
	
		$wpdb->update( 
			MF_TABLE_PANEL_GROUPS, 
			array( 
				'name' => $name,
				'duplicate' => $duplicate,
				'expanded' => $expanded,
				'at_right' => 0
			), 
			array( 'id' => $customGroupId ), 
			array( 
				'%s',
				'%d',
				'%d',
				'%d'
			), 
			array( '%d' )
		);
		
	}
}
