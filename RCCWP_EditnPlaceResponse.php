<?php

if (!isset($_POST['post_id'])) die('Invalid post id');

$postID = $_POST['post_id'];
require( dirname(__FILE__) . '/../../../wp-config.php' );

if (!(is_user_logged_in() && current_user_can('edit_post', $postID)))
	die("Athentication failed!");

// Start saving data
global $wpdb;

$postID = $wpdb->escape($_POST['post_id']);
$field_value = urldecode($_POST['field_value']);
$field_type = $wpdb->escape($_POST['field_type']);
$meta_id = $wpdb->escape($_POST['meta_id']);

$post = & get_post( $postID, ARRAY_A );

switch ($field_type){
	
	case 'EIP_title':
		$post['post_title'] = $field_value ;
		wp_update_post( $post);
		break;
		
	case 'EIP_content':
		$post['post_content'] = $field_value ;
		wp_update_post( $post);
		break;
		
	case 'EIP_textbox':
	case 'EIP_mulittextbox':
		$wpdb->query( "UPDATE $wpdb->postmeta SET meta_value = '".$field_value."' WHERE meta_id = '$meta_id'" );
		break;
}
