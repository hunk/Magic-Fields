<?php

// Magic Fields paths
require_once "RCCWP_Constant.php";

class RCCWP_EditnPlace
{

	function EditnHeader (){
		global $post, $wp_version;

		// Is EIP enabled?
		include_once('RCCWP_Options.php');
		$enableEditnplace = RCCWP_Options::Get('enable-editnplace');
		$eip_highlight_color = RCCWP_Options::Get('eip-highlight-color');
		if (0 == $enableEditnplace) return;

		
		$post_id = $post->ID;

		$MF_URI = MF_URI;
		$nicedit_path = MF_URI."js/nicEdit.js";
		$prototype_path = MF_URI."js/prototype.js";
		$editnplace_path = MF_URI."js/editnplace.js";
		$arrow_image_path = MF_URI."images/arrow.gif";
		
	
		if ( is_user_logged_in() && 
		     current_user_can('edit_posts', $post_id)
			){

		echo <<<EOD

			<script language="JavaScript" type="text/javascript" > 
				var JS_MF_URI = '$MF_URI';
			</script>
			<script type="text/javascript" src="$nicedit_path"></script>
			<script type="text/javascript" src="$prototype_path"></script>
			<script type="text/javascript" src="$editnplace_path"></script>
			
			<style type="text/css">
				
				/*<![CDATA[*/
				
				#savingDiv{
					font-size: medium; 
					font-weight: bold;
				}
				
				.EIP_title:hover, .EIP_content:hover, 
				.EIP_textbox:hover, .EIP_mulittextbox:hover {
					background-color: $eip_highlight_color;
				}
				
				.EIPSaveCancel{
					padding: 5px;
					margin-top: -1px;
					z-index: 1000;
					border-color:#CCC;
					border-width:1px;
					border-style:solid;
					background-color:white;
					position:fixed;
					top:0px !important;
					width:100% !important;
					left: 0px  !important;
					/*position:absolute;
					padding-top:2px;
					padding-bottom:2px;
					z-index: 1000;*/
				}
				
				.EIPSaveStatus{
					position:absolute;
					font-size: 14px;
					z-index: 1000;
				}
				
				.EIPnicPanelDiv{
					position: absolute;
					background-image: url($arrow_image_path);
					width:154px;
					height:38px;
					z-index: 1000;
				}
				
				div.nicEdit-panel{
					background-color: white !important;
					width:140px  !important;
				}
				
				div.nicEdit-panelContain{
					background-color: white !important;
					border-bottom: 0px	!important;
					border-left: 0px	!important;
					border-right: 0px	!important;
					width: 92%	!important;
					margin-left: 2px	!important;
					margin-top: 1px	!important;
				}
				
				.nicEdit-selected{
					/*background-color: #FFFFCC  !important;*/
					border: thin inset   !important;
					padding: 10px;
				}
				.nicEdit-button {
					background-color: white !important;
					border: 0px !important;
				}

				/*]]>*/
			
			</style>

EOD;
		}
	}


}


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
?>