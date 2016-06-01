<?php
	
require( dirname(__FILE__) . "/../../../wp-load.php");

//check if the user  is logged in
global $mf_domain;

if( !( is_user_logged_in() && current_user_can('upload_files') ) )
	die(__("You don't have permission to upload files, contact to the administrator for more information!",$mf_domain));

if(empty($_GET['action'])){
	exit();
}

switch($_GET['action']){
	case  "delete":
		$file = addslashes($_GET['file']);
		$exists = $wpdb->get_row("select * from {$wpdb->postmeta} where meta_value =  '{$file}'");
		
		if(!empty($exists->meta_id)){
			$wpdb->query("DELETE FROM {$wpdb->postmeta} where meta_id = {$exists->meta_id}");
		}
		
		//deleting  file
		unlink(MF_FILES_PATH.$file);
		echo "true";
		exit();
}
