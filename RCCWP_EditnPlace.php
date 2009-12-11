<?php

require_once "RCCWP_Constant.php";

class RCCWP_EditnPlace {
	/**
	 * This function load all the necessary scripts for the 
	 * editnplace feature
	 */
	function EditnPlaceJavascript(){
		wp_enqueue_script(	'EditnPlace',
							MF_URI.'js/editnplace.js',
							array('prototype')
						);
		wp_enqueue_script(	'nicEdit',
							MF_URI.'js/nicEdit.js'
						);
						
		$editnplaceCSSFile = MF_UPLOAD_FILES_DIR.'editnplace.css';
		$editnplaceJSFile  = MF_UPLOAD_FILES_DIR.'editnplacepath.js';
		
		
		//checking if the both files exists
		if(!file_exists($editnplaceCSSFile) || !file_exists($editnplaceJSFile)){
			return false;
		}
		
		wp_register_style('mf_editnplace',MF_FILES_URI.'editnplace.css');
		wp_enqueue_style('mf_editnplace');
		wp_enqueue_script('editnplacepath',MF_FILES_URI.'editnplacepath.js');
	}

	/**
	 * This function load all the stylesheets for the EIP feature
	 */
	function EditnHeader (){
		global $post;
		
		$MF_URI = MF_URI;
		if (current_user_can('edit_posts', $post->ID)){

		echo <<<EOD
			<script language="JavaScript" type="text/javascript" >
				var JS_MF_URI = '$MF_URI';
			</script>
EOD;
		}
	}
}

/** 
 *   TODO review all the EIP_* functions, i think is not used anymore
 */
function EIP_title(){
	global $post;
	$post_id = $post->ID;
	echo " EIP_title "." EIP_postid$post_id ";
}

function EIP_content(){
	global $post;
	$post_id = $post->ID;
	echo " EIP_content "." EIP_postid$post_id ";
}

function EIP_textbox($meta_id){
	global $post;
	$post_id = $post->ID;
	return " EIP_textbox "." EIP_postid$post_id "." EIP_mid_".$meta_id;
}

function EIP_mulittextbox($meta_id){
	global $post;
	$post_id = $post->ID;
	return " EIP_mulittextbox "." EIP_postid$post_id "." EIP_mid_".$meta_id;
}
