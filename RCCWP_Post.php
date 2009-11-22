<?php
/** 
 *  When a posts is saved  this class is called for  check if exists  a write panel with custom fields
 *  if exists  then this processes all the custom fields and save his values in the database
 */
class RCCWP_Post {
	/**
	 *  This function is called when  a post is saves
	 */
	function SaveCustomFields($postId){
		global $flag;
		
		if($flag == 0){
			
			//for this save_post action doesn't execute twice
			$flag = 1;
			
			
			//security
			if(!wp_verify_nonce($_REQUEST['rc-custom-write-panel-verify-key'], 'rc-custom-write-panel'))
				return $postId;
			
			//the user  can edit posts?
			if (!current_user_can('edit_post', $postId)){
				return $postId;
			}
			
			RCCWP_Post::SetCustomWritePanel($postId);
			RCCWP_Post::PrepareFieldsValues($postId);
			RCCWP_Post::SetMetaValues($postId);
	
			return $postId;
		}
	}
		
	/**
	 * Attach a custom write panel to the current post by saving the custom write panel id
	 * as a meta value for the post
	 * 
	 *  @param integer $postId
	 */
	function SetCustomWritePanel($postId) {
		$customWritePanelId = $_POST['rc-cwp-custom-write-panel-id'];
		if (isset($customWritePanelId)) {
			if (!empty($customWritePanelId)) {	
				if (!update_post_meta($postId, RC_CWP_POST_WRITE_PANEL_ID_META_KEY, $customWritePanelId)) {
					add_post_meta($postId, RC_CWP_POST_WRITE_PANEL_ID_META_KEY, $customWritePanelId);
				}
			} else {
				delete_post_meta($postId, RC_CWP_POST_WRITE_PANEL_ID_META_KEY);
			}
		}
	}
	
	/**
	 * Save all custom field values meta values for the post, this function assumes that 
	 * $_POST['rc_cwp_meta_keys'] contains the names of the fields, while $_POST[{FIELD_NAME}]
	 * contains the value of the field named {FIELD_NAME}
	 *
	 * @param integer $postId
	 * @return void
	 */
	function SetMetaValues($postId){
		global $wpdb;
	
		$customWritePanelId = $_POST['rc-cwp-custom-write-panel-id'];
		$customFieldKeys = $_POST['rc_cwp_meta_keys'];
		
		if (!empty($customWritePanelId) && !empty($customFieldKeys) ) {
				
			// --- Delete old values
			foreach ($customFieldKeys as $key)
			{
				if (!empty($key))
				{
					list($customFieldId, $groupCounter, $fieldCounter, $groupId, $rawCustomFieldName) = split("_", $key, 5);
					$customFieldName = $wpdb->escape(stripslashes(trim(RC_Format::GetFieldName($rawCustomFieldName))));
					delete_post_meta($postId, $customFieldName);	
				}
			}

			if ( $the_post = wp_is_post_revision($postId) )
				$postId = $the_post;

			$wpdb->query("DELETE FROM ". MF_TABLE_POST_META .
				" WHERE post_id=$postId");

			
			$arr = ARRAY();
			foreach($customFieldKeys as $key=>$value) {
				list($customFieldId, $groupCounter, $fieldCounter, $groupId,$rawCustomFieldName) = split("_", $value, 5);
				$arr[$key]->id = $customFieldId ;
				$arr[$key]->gc = $groupCounter ;
				$arr[$key]->fc = $fieldCounter ;
				$arr[$key]->gi = $groupId;
				$arr[$key]->fn = $rawCustomFieldName ;
				$arr[$key]->ov = $value ;
			}

			// --- Add new meta data
			foreach ($arr as $key)
			{
				if (!empty($key))
				{
					//order
					if($key->gi == 1){
						$order = 1;
					}else if (!empty($_POST['order_'.$key->gi.'_'.$key->gc])){
						$order = $_POST['order_'.$key->gi.'_'.$key->gc];
					}else{
						$order = 1;
					}
					
					$customFieldValue = $_POST[$key->ov];

					$customFieldName = $wpdb->escape(stripslashes(trim(RC_Format::GetFieldName($key->fn))));
					
					// Prepare field value
					if (is_array($customFieldValue))
					{
						$finalValue = array();
						foreach ($customFieldValue as $value)
						{
							$value = stripslashes(trim($value));
							array_push($finalValue, $value);
						}
					}
					else
					{
						$finalValue = stripslashes(trim($customFieldValue));
					}
			
					// Add field value meta data
					add_post_meta($postId, $customFieldName, $finalValue);
					
					// make sure meta is added to the post, not a revision
					if ( $the_post = wp_is_post_revision($postId) )
						$postId = $the_post;
					
					$fieldMetaID = $wpdb->insert_id;

					// Add field extended properties
					$wpdb->query("INSERT INTO ". MF_TABLE_POST_META .
								" (id, field_name, group_count, field_count, post_id,order_id) ".
								" VALUES ($fieldMetaID, '$customFieldName', ".$key->gc.", ".$key->fc.", $postId,$order)");
				}
			}
	 	}	
	}
	
	/**
	 * This function prepares some custom fields before saving it. It reads $_REQUEST and:
	 * 1. Adds params to photos uploaded (Image field)
	 * 2. Formats dates (Date Field) 
	 * 
	 *  @param integer postId
	 */
	function PrepareFieldsValues($postId) {
		global $wpdb;
			
		// Add params to photos
		if( isset( $_REQUEST['rc_cwp_meta_photos'])) {
			
			foreach( $_REQUEST['rc_cwp_meta_photos'] as $meta_name ) {		
				$slashPos = strrpos($_POST[$meta_name], "/");
				if (!($slashPos === FALSE))
					$_POST[$meta_name] = substr($_POST[$meta_name], $slashPos+1);
				
				//Rename photo if it is edited using editnplace to avoid phpthumb cache
				if ($_POST[$meta_name.'_dorename'] == 1){
					$oldFilename = $_POST[$meta_name]; 
					$newFilename = time().substr($oldFilename, 10);
					rename(MF_UPLOAD_FILES_DIR.$oldFilename, MF_UPLOAD_FILES_DIR.$newFilename);
					$_POST[$meta_name] = $newFilename;
				}
				
				//if is deleted
				if($_POST[$meta_name.'_deleted'] == 1){	
					$file = $_POST[$meta_name];
					//deleting  the file
					unlink(MF_FILES_PATH.$file);
					$_POST[$meta_name] = '';
				}
			}
		}

		// Format Dates
		if( isset( $_REQUEST['rc_cwp_meta_date'])){
			foreach( $_REQUEST['rc_cwp_meta_date'] as $meta_name ) {
				$metaDate = strtotime($_POST[$meta_name]);
				$formatted_date = date('Y-m-d',$metaDate);
				$_POST[$meta_name] = $formatted_date;
			}
		}
	}
	
	/**
	 * Get a custom write panel by reading $_REQUEST['custom-write-panel-id'] or the
	 * To see whether $_GET['post'] has a custom write panel associated to it.
	 *
	 * @return Custom Write Panel as an object, returns null if there is no write panels.
	 */
	function GetCustomWritePanel()
	{
	    global $wpdb;
		
		if (isset($_GET['post']))
		{

			$customWritePanelId = get_post_meta((int)$_GET['post'], RC_CWP_POST_WRITE_PANEL_ID_META_KEY, true);
		
		
			if (empty($customWritePanelId))
			{
				$customWritePanelId = (int)$_REQUEST['custom-write-panel-id'];
			}
		}
		else if (function_exists('icl_t') && isset($_GET['trid']) )
		{
		    $element_id = $wpdb->get_col("SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE element_type='post' AND trid = ".intval($_GET['trid']));
			$customWritePanelId = get_post_meta((int)$element_id, RC_CWP_POST_WRITE_PANEL_ID_META_KEY, true);

			if (empty($customWritePanelId))
			{
				$customWritePanelId = (int)$_REQUEST['custom-write-panel-id'];
			}

		}
		else if (isset($_REQUEST['custom-write-panel-id']))
		{
			$customWritePanelId = (int)$_REQUEST['custom-write-panel-id'];
		}
		
		if (isset($customWritePanelId)) {
			include_once('RCCWP_Application.php');
			$customWritePanel = RCCWP_CustomWritePanel::Get($customWritePanelId);
		}
		
		return $customWritePanel;
	}

	/**
 	 *  This Method is Executed when a post is deleted
 	 *  @param integer $postId
  	 *  @TODO  check if  is deleted the  values in wp_postmeta too 
 	 */
	function DeletePostMetaData($postId) {
		global $wpdb;
		$wpdb->query("DELETE FROM " . MF_TABLE_POST_META . " WHERE post_id =" . $postId) ;
	}	
}
