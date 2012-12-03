<?php
	
require( dirname(__FILE__) . "/../../../wp-load.php");

//check if the user  is logged in
global $mf_domain;
if(!(is_user_logged_in() &&
      (current_user_can('edit_posts') || current_user_can('edit_published_pages'))))
	die(__("Athentication failed!",$mf_domain));

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
