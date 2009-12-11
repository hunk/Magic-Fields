<?php
/**
 *  When is created, saved,  deleted a Post with write panels this  class has a method accord with 
 *  the action executed
 * 
 */
class RCCWP_Processor {
	
	/**
	 *  This function is executed every time to something related with the Magic Fields happen
	 *  this function update,delete,create a customfield,writepanel,group.
	 */
	function Main() {
		require_once('RC_Format.php');
		global $CUSTOM_WRITE_PANEL;
		
		if (isset($_POST['edit-with-no-custom-write-panel']))
		{
			$type = RCCWP_Post::GetCustomWritePanel();
			if( is_object($type) )
				$ptype = $type->type;
			else
				$ptype = (strpos($_SERVER['REQUEST_URI'], 'page.php') !== FALSE ) ? 'page' : 'post';
			wp_redirect($ptype.'.php?action=edit&post=' . $_POST['post-id'] . '&no-custom-write-panel');
		}
		else if (isset($_POST['edit-with-custom-write-panel']) && isset($_POST['custom-write-panel-id']) && (int) $_POST['custom-write-panel-id'] > 0)
		{
			$type = RCCWP_Post::GetCustomWritePanel();
			if( is_object($type) )
				$ptype = $type->type;
			else
				$ptype = (strpos($_SERVER['REQUEST_URI'], 'page.php') !== FALSE ) ? 'page' : 'post';
			wp_redirect($type->type.'.php?action=edit&post=' . $_POST['post-id'] . '&custom-write-panel-id=' . $_POST['custom-write-panel-id']);
		}
	
		if(empty($_REQUEST['mf_action'])){
			$currentAction = "";
		}else{
			$currentAction = $_REQUEST['mf_action'];
		}
		switch ($currentAction){
			
			// ------------ Write Panels
			case 'finish-create-custom-write-panel':
				include_once('RCCWP_CustomWritePanel.php');
					
				$default_theme_page=NULL;
				if($_POST['radPostPage'] == 'page'){ 
					$default_theme_page = $_POST['page_template']; 
					$default_parent_page = $_POST['parent_id'];
				}
				
				$customWritePanelId = RCCWP_CustomWritePanel::Create(
					$_POST['custom-write-panel-name'],
					$_POST['custom-write-panel-description'],
					$_POST['custom-write-panel-standard-fields'],
					$_POST['custom-write-panel-categories'],
					$_POST['custom-write-panel-order'],
					FALSE,
					true,
					$_POST['single'],
					$default_theme_page,
					$default_parent_page
				);

				wp_redirect(RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('view-custom-write-panel', $customWritePanelId));
				break;
				
			case 'submit-edit-custom-write-panel':
				include_once('RCCWP_CustomWritePanel.php');
				
				$default_theme_page=NULL;
				if($_POST['radPostPage'] == 'page'){ 
					$default_theme_page = $_POST['page_template'];
					$default_parent_page = $_POST['parent_id'];
				}

				RCCWP_CustomWritePanel::Update(
					$_POST['custom-write-panel-id'],
					$_POST['custom-write-panel-name'],
					$_POST['custom-write-panel-description'],
					$_POST['custom-write-panel-standard-fields'],
					$_POST['custom-write-panel-categories'],
					$_POST['custom-write-panel-order'],
					FALSE,
					true,
					$_POST['single'],
					$default_theme_page,
					$default_parent_page
				);
				
				RCCWP_CustomWritePanel::AssignToRole($_POST['custom-write-panel-id'], 'administrator');
				break;
				
				
			case 'export-custom-write-panel':				
				require_once('RCCWP_CustomWritePanel.php');	
				$panelID = $_REQUEST['custom-write-panel-id'];
				$writePanel = RCCWP_CustomWritePanel::Get($panelID);
				$exportedFilename = $tmpPath = sys_get_temp_dir().DIRECTORY_SEPARATOR. $writePanel->name . '.pnl';
				
				RCCWP_CustomWritePanel::Export($panelID, $exportedFilename);
	
				// send file in header
				header('Content-type: binary');
				header('Content-Disposition: attachment; filename="'.$writePanel->name.'.pnl"');
				readfile($exportedFilename);
				unlink($exportedFilename);
				exit();	
				break;
				
			case 'delete-custom-write-panel':
				include_once('RCCWP_CustomWritePanel.php');
				RCCWP_CustomWritePanel::Delete($_GET['custom-write-panel-id']);
				break;
			// ------------ Groups
			case 'finish-create-custom-group':
				include_once('RCCWP_CustomGroup.php');
				$customGroupId = RCCWP_CustomGroup::Create(
						$_POST['custom-write-panel-id'], $_POST['custom-group-name'], $_POST['custom-group-duplicate'], $_POST['custom-group-at_right']);
				break;
				
			case 'delete-custom-group':
				include_once('RCCWP_CustomGroup.php');
				$customGroup = RCCWP_CustomGroup::Get((int)$_REQUEST['custom-group-id']);
				RCCWP_CustomGroup::Delete($_GET['custom-group-id']);
				break;

			case 'submit-edit-custom-group':				
				include_once('RCCWP_CustomGroup.php');
				RCCWP_CustomGroup::Update(
					$_REQUEST['custom-group-id'],
					$_POST['custom-group-name'],
					$_POST['custom-group-duplicate'],
					$_POST['custom-group-at_right']);
				break;
										
			// ------------ Fields
			case 'copy-custom-field':
				include_once('RCCWP_CustomField.php');
				$fieldToCopy = RCCWP_CustomField::Get($_REQUEST['custom-field-id']);
				
				if (RCCWP_Processor::CheckFieldName($fieldToCopy->name, $_REQUEST['custom-write-panel-id'])){
					$newURL = RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('create-custom-field').'&custom-group-id='.$_REQUEST['custom-group-id'].'&err_msg=-1';
					wp_redirect($newURL);
					exit;
				}
								
				RCCWP_CustomField::Create(
					$_REQUEST['custom-group-id'],
					$fieldToCopy->name,
					$fieldToCopy->description,
					$fieldToCopy->display_order,
					$fieldToCopy->required_field,
					$fieldToCopy->type_id,
					$fieldToCopy->options,
					$fieldToCopy->default_value,
					$fieldToCopy->properties,
					$fieldToCopy->duplicate,
					$fieldToCopy->helptext
					);
				
			case 'continue-create-custom-field':
				if (RCCWP_Processor::CheckFieldName($_POST['custom-field-name'], $_REQUEST['custom-write-panel-id'])){
					$newURL = RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('create-custom-field').'&custom-group-id='.$_REQUEST['custom-group-id'].'&err_msg=-1';
					wp_redirect($newURL);
					exit;
				}
				break;
				
			case 'finish-create-custom-field':
				include_once('RCCWP_CustomField.php');
				
				if (RCCWP_Processor::CheckFieldName($_POST['custom-field-name'], $_REQUEST['custom-write-panel-id'])){
					$newURL = RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('create-custom-field').'&custom-group-id='.$_REQUEST['custom-group-id'].'&err_msg=-1';
					wp_redirect($newURL);
					exit;
				}
					
				$current_field = RCCWP_CustomField::GetCustomFieldTypes((int)$_REQUEST['custom-field-type']);
				
				if ($current_field->has_properties)
				{
					$custom_field_properties = array();
					if (in_array($current_field->name, array('Textbox', 'Listbox')))
					{
						$custom_field_properties['size'] = $_POST['custom-field-size'];
					}
					else if (in_array($current_field->name, array('Multiline Textbox')))
					{
						$custom_field_properties['height'] = $_POST['custom-field-height'];
						$custom_field_properties['width'] = $_POST['custom-field-width'];
					}
					else if (in_array($current_field->name, array('Date')))
					{
						$custom_field_properties['format'] = $_POST['custom-field-date-format'];
					}
					else if( in_array( $current_field->name, array('Image') ) )
					{
						$params = '';
						if( $_POST['custom-field-photo-height'] != '' && is_numeric( $_POST['custom-field-photo-height']) )
						{
							$params .= '&h=' . $_POST['custom-field-photo-height'];
						}
	
						if( $_POST['custom-field-photo-width'] != '' && is_numeric( $_POST['custom-field-photo-width']) )
						{
							$params .= '&w=' . $_POST['custom-field-photo-width'];
						}
						
						if( $_POST['custom-field-custom-params'] != '' )
						{
							$params .= '&' . $_POST['custom-field-custom-params'];
						}
	
						if( $params )
						{
							$custom_field_properties['params'] = $params;
						}
					}
					else if (in_array($current_field->name, array('Date')))
					{
						$custom_field_properties['format'] = $_POST['custom-field-date-format'];
					}
					else if (in_array($current_field->name, array('Slider')))
					{
						$custom_field_properties['max'] = $_POST['custom-field-slider-max'];
						$custom_field_properties['min'] = $_POST['custom-field-slider-min'];
						$custom_field_properties['step'] = $_POST['custom-field-slider-step'];
					}
					//eeble
					else if (in_array($current_field->name, array('Related Type')))
					{
						$custom_field_properties['panel_id'] = $_POST['custom-field-related-type-panel-id'];
					}
				}
				
				RCCWP_CustomField::Create(
					$_POST['custom-group-id'],
					$_POST['custom-field-name'],
					$_POST['custom-field-description'],
					$_POST['custom-field-order'],
					$_POST['custom-field-required'],
					$_POST['custom-field-type'],
					$_POST['custom-field-options'],
					$_POST['custom-field-default-value'],
					$custom_field_properties,
					$_POST['custom-field-duplicate'],
					$_POST['custom-field-helptext']
					);
				break;
				
			case 'submit-edit-custom-field':
				
				include_once('RCCWP_CustomField.php');
				
				
				$current_field_obj = RCCWP_CustomField::Get($_POST['custom-field-id']);
				if ($_POST['custom-field-name']!=$current_field_obj->name && RCCWP_Processor::CheckFieldName($_POST['custom-field-name'], $_REQUEST['custom-write-panel-id'])){
					$newURL = RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('edit-custom-field').'&custom-field-id='.$_POST['custom-field-id'].'&err_msg=-1';
					wp_redirect($newURL);
					exit;
				}
				
				$current_field = RCCWP_CustomField::GetCustomFieldTypes((int)$_POST['custom-field-type']);
				
				if ($current_field->has_properties)
				{
					$custom_field_properties = array();
					if (in_array($current_field->name, array('Textbox', 'Listbox')))
					{
						$custom_field_properties['size'] = $_POST['custom-field-size'];
					}
					else if (in_array($current_field->name, array('Multiline Textbox')))
					{
						$custom_field_properties['height'] = $_POST['custom-field-height'];
						$custom_field_properties['width'] = $_POST['custom-field-width'];
					}
					else if( in_array( $current_field->name, array('Image') ) )
					{ 
						$params = '';
						
						if( $_POST['custom-field-photo-height'] != '' && is_numeric( $_POST['custom-field-photo-height']) )
						{
							$params = '&h=' . $_POST['custom-field-photo-height'];
						}
	
						if( $_POST['custom-field-photo-width'] != '' && is_numeric( $_POST['custom-field-photo-width']) )
						{
							$params .= '&w=' . $_POST['custom-field-photo-width'];
						}
						
						if( $_POST['custom-field-custom-params'] != '' )
						{
							$params .= '&' . $_POST['custom-field-custom-params'];
						}
	
						if( $params )
						{
							$custom_field_properties['params'] = $params;
						}
					}
					else if (in_array($current_field->name, array('Date')))
					{
						$custom_field_properties['format'] = $_POST['custom-field-date-format'];
					}
					else if (in_array($current_field->name, array('Slider')))
					{
						$custom_field_properties['max'] = $_POST['custom-field-slider-max'];
						$custom_field_properties['min'] = $_POST['custom-field-slider-min'];
						$custom_field_properties['step'] = $_POST['custom-field-slider-step'];
					}else if (in_array($current_field->name, array('Related Type'))) {
						$custom_field_properties['panel_id'] = $_POST['custom-field-related-type-panel-id'];
					}
				}
				
				RCCWP_CustomField::Update(
					$_POST['custom-field-id'],
					$_POST['custom-field-name'],
					$_POST['custom-field-description'],
					$_POST['custom-field-order'],
					$_POST['custom-field-required'],
					$_POST['custom-field-type'],
					$_POST['custom-field-options'],
					$_POST['custom-field-default-value'],
					$custom_field_properties,
					$_POST['custom-field-duplicate'],
					$_POST['custom-field-helptext']
					);
					
				break;
				
			case 'delete-custom-field':
				
				include_once('RCCWP_CustomField.php');
				
				if(isset($_REQUEST['custom-group-id']) && !empty($_REQUEST['custom-group-id']) )
					$customGroupId = (int)$_REQUEST['custom-group-id'];
	
				$customGroup = RCCWP_CustomGroup::Get($customGroupId);
	
				RCCWP_CustomField::Delete($_REQUEST['custom-field-id']);
	
				break;

			default:
								
				if (RCCWP_Application::InWritePostPanel())
				{
					include_once('RCCWP_Menu.php');
					include_once('RCCWP_WritePostPage.php');
					
					$CUSTOM_WRITE_PANEL = RCCWP_Post::GetCustomWritePanel();
					
					
					if (isset($CUSTOM_WRITE_PANEL) && $CUSTOM_WRITE_PANEL > 0){
								
						ob_start(array('RCCWP_WritePostPage', 'ApplyCustomWritePanelAssignedCategories'));
						
						add_action('admin_head',
						 			array(	'RCCWP_WritePostPage', 
											'CustomFieldsCSS'
										 )
								  );	
								
						//adding javascripts files for the custom fields
						add_action('admin_print_scripts',
									array(	'RCCWP_WritePostPage',
											'CustomFieldsJavascript'
										)
									);
									
						add_action('admin_head',
						 			array(	'RCCWP_WritePostPage',
						 					'ApplyCustomWritePanelHeader'
										)
								  );
					
					
						add_action('admin_menu',
									array(	'RCCWP_WritePostPage',
											'CustomFieldCollectionInterface'
										 )
								  );

					}
					else if (!isset($_REQUEST['no-custom-write-panel']) && isset($_REQUEST['post']))
					{
						include_once('RCCWP_Options.php');
						$promptEditingPost = RCCWP_Options::Get('prompt-editing-post');
						if ($promptEditingPost == 1)
						{
							wp_redirect('?page=' . urlencode(MF_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php') . '&assign-custom-write-panel=' . (int)$_GET['post']);
						}
					}
				} else if (isset($_POST['update-custom-write-panel-options'])) {
					if ($_POST['uninstall-custom-write-panel'] == 'uninstall') {
						RCCWP_Application::Uninstall();
						wp_redirect('options-general.php');
					} else {
						include_once('RCCWP_Options.php');
						
						$options['hide-write-post'] = $_POST['hide-write-post'];
						$options['hide-write-page'] = $_POST['hide-write-page'];
						$options['hide-visual-editor'] = $_POST['hide-visual-editor'];
						$options['prompt-editing-post'] = $_POST['prompt-editing-post'];
						$options['assign-to-role'] = $_POST['assign-to-role'];
						$options['use-snipshot'] = $_POST['use-snipshot'];
						$options['enable-editnplace'] = $_POST['enable-editnplace'];
						$options['eip-highlight-color'] = $_POST['eip-highlight-color'];
						$options['enable-swfupload'] = $_POST['enable-swfupload'] ;
						$options['enable-browserupload'] = $_POST['enable-browserupload'];
						$options['default-custom-write-panel'] = $_POST['default-custom-write-panel'];
						$options['enable-HTMLPurifier'] = $_POST['enable-HTMLPurifier'];
						$options['tidy-level'] = $_POST['tidy-level'];
						$options['canvas_show_instructions'] = $_POST['canvas_show_instructions'];
						$options['canvas_show_zone_name'] = $_POST['canvas_show_zone_name'];
						$options['canvas_show'] = $_POST['canvas_show'];
						$options['ink_show'] = $_POST['ink_show'];
						$options['hide-non-standart-content'] = $_POST['hide-non-standart-content'];
						$options['condense-menu'] = $_POST['condense-menu'];
						
						RCCWP_Options::Update($options);
						$EnP = RCCWP_Application::create_EditnPlace_css(TRUE);
					}
				}
		}
		
	}
	/**
	 *   Flush All the  buffers
	 */
	function FlushAllOutputBuffer() { 
		while (@ob_end_flush()); 
	} 
	
	/**
	 *  Redirect Function
	 *  @param string $location
	 */
	function Redirect($location)
	{
		global $post_ID;
		global $page_ID;

		
		if (!empty($_REQUEST['rc-cwp-custom-write-panel-id']))
		{
			if (strstr($location, 'post-new.php?posted=') || strstr($location, 'page-new.php?posted='))
			{
				$id = ($post_ID=="")?$page_ID:$post_ID;
				$location = $_REQUEST['_wp_http_referer'] . '&posted=' . $id;
			}
		}
		return $location;
	}
	
	/**
	 *  Check if the name of some custom field is already used
	 *  @param string $fieldName
	 *  @param int  the Write panel ID
	 *  @return bool
	 */
	function CheckFieldName($fieldName, $panelID){
		global $wpdb;
		
		$sql = "SELECT id, group_id FROM " . MF_TABLE_GROUP_FIELDS .
				" WHERE name='$fieldName' ";
		$results =$wpdb->get_results($sql);
	
		foreach($results as $result){
			$fieldGroup = RCCWP_CustomGroup::Get($result->group_id);
			if ($panelID == $fieldGroup->panel_id){
				return true;
			}
		}
		return false;
	}

}
