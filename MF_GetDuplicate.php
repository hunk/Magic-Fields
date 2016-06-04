<?php

class MF_GetDuplicate {

	public function __construct() {
        add_action( 'wp_ajax_mf_get_duplicate', array( $this, 'resolve' ) );
    }

    function resolve() {
    	global $mf_domain;

    	check_ajax_referer( 'nonce_ajax_duplicate', 'nonce_ajax_duplicate');

    	if ( !(is_user_logged_in() && (current_user_can('edit_posts') || current_user_can('edit_published_pages'))) ) {
    		echo __("Athentication failed",$mf_domain);
    		wp_die();
    	}

    	if( isset($_POST['flag']) && $_POST['flag'] == "group" ) {
			$customGroup = RCCWP_CustomGroup::Get( $_POST['groupId'] ) ;

			RCCWP_WritePostPage::GroupDuplicate(
											$customGroup,
											$_POST['groupCounter'],
											$_POST['order']
										);
		}else{
	
		 	$customFieldId = $_POST['customFieldId'];
			$groupCounter = $_POST['groupCounter'];
			$fieldCounter = $_POST['fieldCounter'];
			$groupId = $_POST['groupId'];
			RCCWP_WritePostPage::CustomFieldInterface(
														$customFieldId, 
														$groupCounter, 
														$fieldCounter,
														$groupId
													 );
		}

    	wp_die();
    }
}

$mf_get_duplicate = new MF_GetDuplicate();