<?php
require( dirname(__FILE__) . '/../../../wp-load.php' );
global $mf_domain;
if (!(is_user_logged_in() &&
      (current_user_can('edit_posts') || current_user_can('edit_published_pages'))))
	die(__("Athentication failed!",$mf_domain));
	
require_once("RCCWP_WritePostPage.php");
require_once("RCCWP_CustomGroup.php");
require_once('RCCWP_Options.php');

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
