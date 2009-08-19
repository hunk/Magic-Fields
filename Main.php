<?php
/*
Plugin Name: Magic-fields
Plugin URI: http://magicfields.org
Description: Create custom write panels and easily retrieve their values in your templates.
Author: Hunk and Gnuget
Version: 1.0
Author URI: http://magicfields.org
*/


/**
 * This work is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License 
 * as published by the Free Software Foundation; either version 
 * 2 of the License, or any later version.
 *
 * This work is distributed in the hope that it will be useful, 
 * but without any warranty; without even the implied warranty 
 * of merchantability or fitness for a particular purpose. See 
 * Version 2 and version 3 of the GNU General Public License for
 * more details. You should have received a copy of the GNU General 
 * Public License along with this program; if not, write to the 
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, 
 * Boston, MA 02110-1301 USA
 */


// Globals
global $wpdb, $main_page, $table_prefix, $zones, $parents, $installed, $main_page, $post;
global $current_user;
global $wp_filesystem;
global $FIELD_TYPES;

// Classes
require_once 'PanelFields.php';

// Include Flutter API related files

require_once 'RCCWP_CustomGroup.php';

// Classes/Core files

require_once 'RCCWP_Constant.php';

// Include Flutter API related files

require_once 'RCCWP_CustomField.php';
require_once 'RCCWP_CustomWritePanel.php';

// Include files containing Flutter public functions
require_once 'get-custom.php';

// Include other files used in this script
require_once 'RCCWP_Menu.php';
require_once 'RCCWP_CreateCustomFieldPage.php';
require_once 'tools/debug.php';


global $is_wordpress_mu;
if(isset($current_blog)) 
	$is_wordpress_mu=true;
else
	$is_wordpress_mu=false;
	
 /* function for languajes
  *
  */
global $mf_domain;
$mf_domain = 'magic_fields';	
load_plugin_textdomain($mf_domain, '/'.PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/languajes', basename(dirname(__FILE__)).'/languages');

		

if (is_admin()) {
	require_once ('RCCWP_Application.php');
	require_once ('RCCWP_WritePostPage.php');
	
	register_activation_hook(dirname(__FILE__) . '/Main.php', array('RCCWP_Application', 'Install'));

	if(isset($current_blog)) {
		RCCWP_Application::Install();
	    add_action('admin_menu', array('RCCWP_Application', 'ContinueInstallation'));
    }

	if (get_option(RC_CWP_OPTION_KEY) !== false) {
		require_once ('RCCWP_Processor.php');
		add_action('init', array('RCCWP_Processor', 'Main'));
		

		add_action('admin_menu', array('RCCWP_Menu', 'AttachCustomWritePanelMenuItems'));
		add_action('admin_menu', array('RCCWP_Menu', 'DetachWpWritePanelMenuItems'));
		add_action('admin_menu', array('RCCWP_Menu', 'AttachOptionsMenuItem'));
		
		add_filter('posts_where', array('RCCWP_Menu', 'FilterPostsPagesList'));
		add_action('admin_head', array('RCCWP_Menu', 'HighlightCustomPanel'));
		add_action('admin_head', array('RCCWP_CreateCustomFieldPage', 'AddAjaxDynamicList'));

		// -- Hook all functions related to saving posts in order to save custom fields values
		require_once ('RCCWP_Post.php');	
		add_action('save_post', array('RCCWP_Post', 'SaveCustomFields'));
 		add_action('delete_post', array('RCCWP_Post','DeletePostMetaData')) ;
		

		
		add_filter('wp_redirect', array('RCCWP_Processor', 'Redirect'));

		add_action('shutdown', array('RCCWP_Processor', 'FlushAllOutputBuffer'));

		add_action('admin_notices', array('RCCWP_Application', 'CheckInstallation'));  
		add_action('admin_notices', array('RCCWP_WritePostPage', 'FormError'));
	    
	}
}

add_action('admin_print_scripts', array('RCCWP_Menu', 'AddThickbox'));
add_action('admin_menu', array('RCCWP_Menu', 'AttachFlutterMenus'));

require_once ('RCCWP_EditnPlace.php');
add_action('wp_head', array('RCCWP_EditnPlace', 'EditnHeader'));

require_once ('RCCWP_Query.php');
add_action('pre_get_posts', array('RCCWP_Query', 'FilterPrepare'));
add_filter('posts_where', array('RCCWP_Query', 'FilterCustomPostsWhere'));
add_filter('posts_orderby', array('RCCWP_Query', 'FilterCustomPostsOrderby'));
add_filter('posts_fields', array('RCCWP_Query', 'FilterCustomPostsFields'));
add_filter('posts_join_paged', array('RCCWP_Query', 'FilterCustomPostsJoin'));


add_action('edit_page_form','cwp_add_pages_identifiers');
add_action('edit_form_advanced','cwp_add_type_identifier');

// -- KSES filter
add_filter('pre_comment_content','mf_kses');
add_filter('title_save_pre','mf_kses');
//add_filter('content_save_pre','mf_kses');
add_filter('excerpt_save_pre','mf_kses');
add_filter('content_filtered_save_pre','mf_kses');



/**
 *This only one wrapper function for wp_kses
 *this will be used for  passed  and empty array  to the wp_kses function
 *(probably this function will be deprecated soon just i need found a best way to-do this)
 *
 */
function mf_kses($string){

    /**
     * Tags can't be used
     */
     $used_tags = array('select','script','b'); 


    /**
     * List of tags
     */
     $html_tags  = array(
                            'address','applet','area','a','base','basefont','big','blockquote',
                            'body','br','b','caption','center','cite','code','dd','dfn','dir',
                            'div','dl','dt','em','font','form','h1','h2','h3','h4','h5','h6',
                            'head','hr','html','img','input','isindex','i','kbd','link','li',
                            'map','menu','meta','ol','option','param','pre','p','samp','script',
                            'select','small','strike','strong','style','sub','sup','table','td',
                            'textarea','th','title','tr','tt','ul','u','var'
                        );

     //remove that tag to the html_tag list
     foreach($html_tags as $key => $value){
         if(in_array($value,$used_tags)){
            unset($html_tags[$key]);
         }
     }

    return  wp_kses($string,array($html_tags));

}

function cwp_add_type_identifier(){

	global $wpdb;
	global $post;
	
	if( isset($_GET['custom-write-panel-id']) && !empty($_GET['custom-write-panel-id']))
	{
		$getPostID = $wpdb->get_results("SELECT id, type FROM ". MF_TABLE_PANELS ." WHERE id='".$_GET['custom-write-panel-id']."'");
		echo "<input type=\"hidden\" id=\"post_type\" name=\"post_type\" value=\"". $getPostID[0]->type ."\" />";

	}
	else{
		if($post->post_type == 'page') { 
			echo "<input type=\"hidden\" id=\"post_type\" name=\"post_type\" value=\"page\" />";
 		} else {
			echo "<input type=\"hidden\" id=\"post_type\" name=\"post_type\" value=\"post\" />";
 		}

 	}
}

function cwp_add_pages_identifiers(){
	global $post;
	global $wpdb;

	$key = wp_create_nonce('rc-custom-write-panel');
	$id = "";
	$result = $wpdb->get_results( " SELECT meta_value
					FROM $wpdb->postmeta
					WHERE post_id = '$post->ID' and meta_key = '_mf_write_panel_id'", ARRAY_A );
	
	if (count($result) > 0)
		$id = $result[0]['meta_value'];
	echo 
<<<EOF
		<input type="hidden" name="rc-custom-write-panel-verify-key" id="rc-custom-write-panel-verify-key" value="$key" />
		
EOF;
}

if ( !function_exists('sys_get_temp_dir')) {
  function sys_get_temp_dir() {
    if (!empty($_ENV['TMP'])) { return realpath($_ENV['TMP']); }
    if (!empty($_ENV['TMPDIR'])) { return realpath( $_ENV['TMPDIR']); }
    if (!empty($_ENV['TEMP'])) { return realpath( $_ENV['TEMP']); }
    $tempfile=tempnam(uniqid(rand(),TRUE),'');
    if (file_exists($tempfile)) {
    unlink($tempfile);
    return realpath(dirname($tempfile));
    }
  }
}
?>