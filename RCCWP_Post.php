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
			
			//with this  the save_post action don't will be execute twice
			$flag = 1;
			//security
			if(!wp_verify_nonce(@$_REQUEST['rc-custom-write-panel-verify-key'], 'rc-custom-write-panel'))
				return $postId;
			
			//the user  can edit posts?
			if (!(current_user_can('edit_posts', $postId) || current_user_can('edit_published_pages', $postId))){
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
	  
	  if (array_key_exists('rc-cwp-change-custom-write-panel-id', $_POST)) {
		  $customWritePanelId = $_POST['rc-cwp-change-custom-write-panel-id'];
	  } else {
		  $customWritePanelId = $_POST['rc-cwp-custom-write-panel-id'];
    }
    
	  
		if (isset($customWritePanelId)) {
			if (!empty($customWritePanelId) && $customWritePanelId != "-1") {	
			  update_post_meta($postId, RC_CWP_POST_WRITE_PANEL_ID_META_KEY, $customWritePanelId);
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
		
		//delete file
		if(!empty($_POST['magicfields_remove_files'])){
			$files = preg_split('/\|\|\|/', $_POST['magicfields_remove_files']);
			foreach($files as $file){
        do_action('mf_before_delete_file',$file);
				@unlink(MF_FILES_PATH.$file);
			}
		}
		
		
		if(empty($_POST['magicfields'])){
			return true;
		}
		
		$customfields = $_POST['magicfields'];

		if ( $the_post = wp_is_post_revision($postId))
			$postId = $the_post;

		
		if (!empty($customWritePanelId)) {
				
			// --- Delete old values
			foreach($customfields as $name => $field){
				delete_post_meta($postId,$name);
			}

			$wpdb->query("DELETE FROM ". MF_TABLE_POST_META .
				" WHERE post_id=$postId");
			
			//Creating the new values
			//Iterating the custom fields
			foreach($customfields as $name => $groups){
				$groups_index = 1;
				//Iterating the groups
				foreach($groups as $group_id => $fields){ 
					$index = 1;
					//Iterating the  duplicates
					foreach($fields as $value){
						// Adding field value meta data
						add_post_meta($postId, $name, $value);
							
						$fieldMetaID = $wpdb->insert_id;
				
						// Adding  the referencie in the magic fields post meta table
						$wpdb->query("INSERT INTO ". MF_TABLE_POST_META .
										" (id, field_name, group_count, field_count, post_id,order_id) ".
										" VALUES ({$fieldMetaID}, '{$name}',{$groups_index},{$index},{$postId},{$groups_index})"
									);

            //pre save value
            do_action('mf_presave',$fieldMetaID,$name,$groups_index,$index,$postId,$value,$customWritePanelId);

						$index++;
					}
					$groups_index++;
				} 		
			}
			if( MF_GET_CACHE_IS_ON ) {
				purge_cache_dir( MF_GET_CACHE_DIR.$postId );
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
				if( array_key_exists( 'custom-write-panel-id', $_REQUEST ) )
				{
					$customWritePanelId = (int)($_REQUEST['custom-write-panel-id']);
				}else
				{
					$customWritePanelId = 0;
				}
			}
		}
		else if (function_exists('icl_t') && isset($_GET['trid']) )
		{
		    $element_id = $wpdb->get_col("SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE element_type='post' AND trid = ".intval($_GET['trid']));
			$customWritePanelId = get_post_meta((int)$element_id, RC_CWP_POST_WRITE_PANEL_ID_META_KEY, true);

			if (empty($customWritePanelId))
			{
				if( array_key_exists( 'custom-write-panel-id', $_REQUEST ) )
				{
					$customWritePanelId = (int)($_REQUEST['custom-write-panel-id']);
				}else
				{
					$customWritePanelId = 0;
				}
			}

		}
		else if (isset($_REQUEST['custom-write-panel-id']))
		{
			$customWritePanelId = (int)$_REQUEST['custom-write-panel-id'];
		}
		
		$customWritePanel = FALSE;
		if (isset($customWritePanelId)) {
			include_once('RCCWP_Application.php');
			$customWritePanel = RCCWP_CustomWritePanel::Get($customWritePanelId);
		}
		
		return $customWritePanel;
	}

	/**
 	 *  This Method is Executed when a post is deleted
 	 *  @param integer $postId
 	 */
	function DeletePostMetaData($postId) {
		global $wpdb;
		
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
          do_action('mf_before_delete_file',$image->meta_value);
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
	}	
}
