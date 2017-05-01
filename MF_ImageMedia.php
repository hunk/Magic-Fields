<?php

class MF_ImageMedia {

	public function __construct() {
        add_action( 'wp_ajax_mf_get_image_media_info', array( $this, 'get_info' ) );
    }

    function get_info() {
    	global $mf_domain;

    	check_ajax_referer( 'nonce_ajax_get_image_media_info', 'nonce_ajax_get_image_media_info');

    	if( !( is_user_logged_in() && current_user_can('upload_files') ) ) {
    		echo json_encode(
        		array(
        			'success' => false,
            		'error' => "You don't have permission to upload files, contact to the administrator for more information!",$mf_domain
            	)
        	);
    		wp_die();
    	}

    	// remove text aditional in attachment
        $image_id = filter_var($_POST['image_id'], FILTER_SANITIZE_SPECIAL_CHARS);
		$image_id = preg_replace('/del_attachment_/','',$image_id);
		$info = wp_get_attachment_image_src($image_id,'original');

        $field_id = filter_var($_POST['field_id'], FILTER_SANITIZE_SPECIAL_CHARS);
		$field_id = preg_replace('/thumb_/','',$field_id);

		if( count($info) ){
			$image_thumb = PHPTHUMB.'?&w=150&h=120&src='.$info[0];
            $data = array('success'=>true,'image' => $image_thumb,'field_id' => $field_id,'image_value' => $image_id);
		  	echo json_encode($data);
		} else {
			echo json_encode(
        		array(
        			'success' => false,
            		'error' => "The image does not exist",$mf_domain
            	)
        	);

		}

    	wp_die();
    }
}

$mf_get_image_media = new MF_ImageMedia();