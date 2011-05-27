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
		global $CUSTOM_WRITE_PANEL,$wp_version;
		
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
			if(substr($wp_version, 0, 3) >= 3.0){
					$ptype = 'post';
			}else{
					$type = RCCWP_Post::GetCustomWritePanel(); 
					if( is_object($type) )
							$ptype = $type->type;
					else
							$ptype = (strpos($_SERVER['REQUEST_URI'], 'page.php') !== FALSE ) ? 'page' : 'post';
			}
			wp_redirect($ptype.'.php?action=edit&post=' . $_POST['post-id'] . '&custom-write-panel-id=' . $_POST['custom-write-panel-id']);
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
					$default_parent_page,
					$_POST['custom-write-panel-expanded']
				);

				wp_redirect(RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('view-custom-write-panel', $customWritePanelId));
				break;
				
			case 'submit-edit-custom-write-panel':
				include_once('RCCWP_CustomWritePanel.php');
				
				$default_theme_page = $default_parent_page = NULL;
				if($_POST['radPostPage'] == 'page'){ 
					$default_theme_page = $_POST['page_template'];
					$default_parent_page = $_POST['parent_id'];
				}
				
				$default = array(
  				  'custom-write-panel-id' => '',
  				  'custom-write-panel-name' => '',
  				  'custom-write-panel-standard-fields' => '',
  				  'custom-write-panel-categories' => '',
  				  'custom-write-panel-order' => '',
  				  'single' => '',
  				  'theme_page' => '',
  				  'parent_page' => '',
  				  'expanded' => ''
  				);

  				$_POST['theme_page'] = $default_theme_page;
  				$_POST['parent_page'] = $default_parent_page;
  				$save = array_merge($default,$_POST);

				RCCWP_CustomWritePanel::Update(
					$save['custom-write-panel-id'],
					$save['custom-write-panel-name'],
					NULL,
					$save['custom-write-panel-standard-fields'],
					$save['custom-write-panel-categories'],
					$save['custom-write-panel-order'],
					FALSE,
					true,
					$save['single'],
					$save['theme_page'],
					$save['parent_page'],
					$save['custom-write-panel-expanded']
				);
				
				RCCWP_CustomWritePanel::AssignToRole($_POST['custom-write-panel-id'], 'administrator');
				break;
				
				
			case 'export-custom-write-panel':				
				require_once('RCCWP_CustomWritePanel.php');	
				$panelID = $_REQUEST['custom-write-panel-id'];
				$writePanel = RCCWP_CustomWritePanel::Get($panelID);

                             	// send file in header
				header('Content-type: binary');
				header('Content-Disposition: attachment; filename="'.$writePanel->name.'.pnl"');
                                print RCCWP_CustomWritePanel::Export($panelID);
				exit();	
				break;
				
			case 'delete-custom-write-panel':
				include_once('RCCWP_CustomWritePanel.php');
				RCCWP_CustomWritePanel::Delete($_GET['custom-write-panel-id']);
				break;
			// ------------ Groups
			case 'finish-create-custom-group':
				include_once('RCCWP_CustomGroup.php');
				$default = array(
				  'custom-write-panel-id' => '',
				  'custom-group-name' => '',
				  'custom-group-duplicate' => '',
				  'custom-group-expanded' => ''
				);
				$values = array_merge($default,$_POST);
				
				$customGroupId = RCCWP_CustomGroup::Create(
						$values['custom-write-panel-id'], $values['custom-group-name'], $values['custom-group-duplicate'], $values['custom-group-expanded'], NULL);
				break;
				
			case 'delete-custom-group':
				include_once('RCCWP_CustomGroup.php');
				$customGroup = RCCWP_CustomGroup::Get((int)$_REQUEST['custom-group-id']);
				RCCWP_CustomGroup::Delete($_GET['custom-group-id']);
				break;
			 
			case 'unlink-write-panel':
			 global $wpdb;
				$postId = (int)preg_replace('/post-/','',$_REQUEST['post-id']);
				$dashboard = $_REQUEST['dashboard'];
				if($postId){
					//only delete images and postmeta fields with write panels
					if(count(get_post_meta($postId, RC_CWP_POST_WRITE_PANEL_ID_META_KEY))){
						$query = sprintf('SELECT wp_pm.meta_value 
						FROM %s mf_pm, %s mf_cf, %s wp_pm
						WHERE mf_pm.field_name = mf_cf.name AND mf_cf.type = 9 AND mf_pm.post_id = %d AND wp_pm.meta_id = mf_pm.id',
						MF_TABLE_POST_META,
						MF_TABLE_GROUP_FIELDS,
						$wpdb->postmeta,
						$postId
						);
						$images = $wpdb->get_results($query);
						foreach($images as $image){
							if($image->meta_value != ''){
								$tmp = sprintf('%s%s',MF_FILES_PATH,$image->meta_value);
								@unlink($tmp);
							}
						}
						
						//delete all data of postmeta (WP and MF)
						$query = sprintf('DELETE a,b from %s a INNER JOIN %s b WHERE a.meta_id = b.id AND a.post_id = %d',
						$wpdb->postmeta,
						MF_TABLE_POST_META,
						$postId
						);
						$wpdb->query($query);
					}
		
				 delete_post_meta($postId, RC_CWP_POST_WRITE_PANEL_ID_META_KEY);
				 wp_redirect($dashboard);
				 exit();
				}
			 break;

			case 'submit-edit-custom-group':				
				include_once('RCCWP_CustomGroup.php');
				$default = array(
				  'custom-write-panel-id' => '',
				  'custom-group-name' => '',
				  'custom-group-duplicate' => '',
				  'custom-group-expanded' => ''
				);
				$all = $_POST;
				$all['custom-group-id'] = $_REQUEST['custom-group-id'];
				$values = array_merge($default,$all);
				RCCWP_CustomGroup::Update(
					$values['custom-group-id'],
					$values['custom-group-name'],
					$values['custom-group-duplicate'],
					$values['custom-group-expanded'],
					NULL);
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
						if( isset( $_POST['strict-max-length'] ) ) {
							$custom_field_properties['strict-max-length'] = $_POST['strict-max-length'];
							if( empty( $custom_field_properties['size'] ) ) {
								$custom_field_properties['size'] = 10;
							}
						}
					}
					else if (in_array($current_field->name, array('Multiline Textbox')))
					{
						$custom_field_properties['height'] = $_POST['custom-field-height'];
						$custom_field_properties['width'] = $_POST['custom-field-width'];
						if( isset($_POST['hide-visual-editor']) ) $custom_field_properties['hide-visual-editor'] = 1;
						if( isset( $_POST['strict-max-length'] ) ) {
							$custom_field_properties['hide-visual-editor'] = 1;
							$custom_field_properties['strict-max-length'] = $_POST['strict-max-length'];
							if( empty( $custom_field_properties['height'] ) ) {
								$custom_field_properties['height'] = 4;
							}
							if( empty( $custom_field_properties['width'] ) ) {
								$custom_field_properties['width'] = 64;
							}
						}
					}
					else if (in_array($current_field->name, array('Date')))
					{
						$custom_field_properties['format'] = $_POST['custom-field-date-format'];
					}
					else if( in_array( $current_field->name, array('Image','Image (Upload Media)') ) )
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
				
				$default = array(
				  'custom-group-id' => '',
				  'custom-field-name' => '',
				  'custom-field-description' => '',
				  'custom-field-order' => '',
				  'custom-field-required' => '',
				  'custom-field-type' => '',
				  'custom-field-options' => '',
				  'custom-field-default-value' => '',
				  'prop' => '',
				  'custom-field-duplicate' => '',
				  'custom-field-helptext' => ''
				);
				
				$_POST['prop'] = $custom_field_properties;
				$save = array_merge($default,$_POST);
				
				RCCWP_CustomField::Create(
					$save['custom-group-id'],
					$save['custom-field-name'],
					$save['custom-field-description'],
					$save['custom-field-order'],
					$save['custom-field-required'],
					$save['custom-field-type'],
					$save['custom-field-options'],
					$save['custom-field-default-value'],
					$save['prop'],
					$save['custom-field-duplicate'],
					$save['custom-field-helptext']
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
						if( isset( $_POST['strict-max-length'] ) ) {
							$custom_field_properties['strict-max-length'] = $_POST['strict-max-length'];
							if( empty( $custom_field_properties['size'] ) ) {
								$custom_field_properties['size'] = 10;
							}
						}
					}
					else if (in_array($current_field->name, array('Multiline Textbox')))
					{
						$custom_field_properties['height'] = $_POST['custom-field-height'];
						$custom_field_properties['width'] = $_POST['custom-field-width'];
						if( isset($_POST['hide-visual-editor']) ) $custom_field_properties['hide-visual-editor'] = 1;
						if( isset( $_POST['strict-max-length'] ) ) {
							$custom_field_properties['hide-visual-editor'] = 1;
							$custom_field_properties['strict-max-length'] = $_POST['strict-max-length'];
							if( empty( $custom_field_properties['height'] ) ) {
								$custom_field_properties['height'] = 4;
							}
							if( empty( $custom_field_properties['width'] ) ) {
								$custom_field_properties['width'] = 64;
							}
						}
					}
					else if( in_array( $current_field->name, array('Image','Image (Upload Media)') ) )
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
				
				$default = array(
  			  'custom-group-id' => '',
  				'custom-field-name' => '',
  				'custom-field-description' => '',
  				'custom-field-order' => '',
  				'custom-field-required' => '',
  				'custom-field-type' => '',
  				'custom-field-options' => '',
  				'custom-field-default-value' => '',
  				'prop' => '',
  				'custom-field-duplicate' => '',
  				'custom-field-helptext' => ''
  			);

  			$_POST['prop'] = $custom_field_properties;
  			$save = array_merge($default,$_POST);
				
				RCCWP_CustomField::Update(
					$save['custom-field-id'],
					$save['custom-field-name'],
					$save['custom-field-description'],
					$save['custom-field-order'],
					$save['custom-field-required'],
					$save['custom-field-type'],
					$save['custom-field-options'],
					$save['custom-field-default-value'],
					$save['prop'],
					$save['custom-field-duplicate'],
					$save['custom-field-helptext']
					);
					
				break;
				
			case 'delete-custom-field':
				
				include_once('RCCWP_CustomField.php');
				
				if(isset($_REQUEST['custom-field-id']) && !empty($_REQUEST['custom-field-id']) )
					RCCWP_CustomField::Delete($_REQUEST['custom-field-id']);
	
				break;
      case 'save-fields-order':
        RCCWP_CustomWritePanelPage::save_order_fields();

			default:
								
  			
  							  
				if (RCCWP_Application::InWritePostPanel())
				{
					include_once('RCCWP_Menu.php');
					include_once('RCCWP_WritePostPage.php');
					
					$CUSTOM_WRITE_PANEL = RCCWP_Post::GetCustomWritePanel();
					
					if (isset($CUSTOM_WRITE_PANEL) && !empty($CUSTOM_WRITE_PANEL) ){
						
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
						
						add_action('admin_print_scripts',
							array(	'RCCWP_WritePostPage',
								'ApplyWritePanelAssignedCategoriesOrTemplate'
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
						
						if( isset($_POST['clear-cache-image-mf']) ){
							$dir = MF_CACHE_DIR;

							if (is_dir($dir)) {
						    if ($dh = opendir($dir)) {
					        while (($file = readdir($dh)) !== false) {
										if(!is_dir($file)){
											@unlink(MF_CACHE_DIR.$file);
										}
					        }
									closedir($dh);
								}
							}
						}
						
						include_once('RCCWP_Options.php');
						
						$default = array(
        		  'condense-menu' => 0,
        			'hide-non-standart-content' => 0,
        			'hide-write-post' => 0,
        			'hide-write-page' => 0,
        			'hide-visual-editor' => 0,
                          'dont-remove-tmce' => 0,
        			'prompt-editing-post' => 0,
        			'assign-to-role' => 0,
        			'default-custom-write-panel' => 0
        		);
        		
        		$save_options = $_POST;
						unset($save_options['uninstall-custom-write-panel']);
						unset($save_options['update-custom-write-panel-options']);
        		
        		$save = array_merge($default,$save_options);
						
						RCCWP_Options::Update($save);
						
					}
				}
				
				if (isset($_REQUEST['post'])) {
  				// traversal addition to change write panel
          add_action('admin_menu',
    						array(	'RCCWP_WritePostPage',
    								'CreateAttributesBox'
    							 )
    					  );
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
