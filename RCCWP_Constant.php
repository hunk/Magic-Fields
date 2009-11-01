<?php

global $wpdb,$is_wordpress_mu,$blog_id;

if (!defined('DIRECTORY_SEPARATOR'))
{
	if (strpos(php_uname('s'), 'Win') !== false )
		define('DIRECTORY_SEPARATOR', '\\');
	else 
		define('DIRECTORY_SEPARATOR', '/');
}

// General Constants
define('RC_CWP_DB_VERSION', 3);
define('RC_CWP_POST_WRITE_PANEL_ID_META_KEY', '_mf_write_panel_id');
define('RC_CWP_OPTION_KEY', 'mf_custom_write_panel');


// Magic Fields paths
preg_match('/wp-content(.*)(RCCWP_Constant\.php)$/',__FILE__,$mfpath);
$mfpath = str_replace('\\', '/', $mfpath);
define('MF_PLUGIN_DIR', dirname(plugin_basename(__FILE__))); 
define("MF_PATH", dirname(__FILE__));

define("MF_URI", get_bloginfo('wpurl').'/wp-content'.$mfpath[1]); 
define("MF_URI_RELATIVE", 'wp-content'.$mfpath[1]);
define("PHPTHUMB",MF_URI."thirdparty/phpthumb/phpThumb.php");

//prefix all tables
if(isset($current_blog)){
	$mf_prefix=$wpdb->base_prefix;
}else{
	$mf_prefix=$wpdb->prefix;
}
// -- Tables names

// Tables containing somehow constant data
define('MF_TABLE_CUSTOM_FIELD_TYPES', $mf_prefix  . 'mf_custom_field_types');
//TODO: check this table
define('MF_TABLE_STANDARD_FIELDS', $mf_prefix  . 'mf_standard_fields');

// Panels - Groups - Fields
define('MF_TABLE_PANELS', $mf_prefix  . 'mf_write_panels');
define('MF_TABLE_PANEL_GROUPS', $mf_prefix  . 'mf_module_groups');
define('MF_TABLE_GROUP_FIELDS', $mf_prefix  . 'mf_panel_custom_field');

// Extra information about panels
define('MF_TABLE_PANEL_CATEGORY', $mf_prefix  . 'mf_panel_category');
define('MF_TABLE_PANEL_STANDARD_FIELD', $mf_prefix  . 'mf_panel_standard_field');
// TODO: check this table
define('MF_TABLE_PANEL_HIDDEN_EXTERNAL_FIELD', $mf_prefix  . 'mf_panel_hidden_external_field');

// Extra information about fields
define('MF_TABLE_CUSTOM_FIELD_OPTIONS', $mf_prefix  . 'mf_custom_field_options');
define('MF_TABLE_CUSTOM_FIELD_PROPERTIES', $mf_prefix  . 'mf_custom_field_properties');

// Extra information about post meta values.
define('MF_TABLE_POST_META', $wpdb->prefix . 'mf_post_meta');

// Field Types
global $FIELD_TYPES;
$FIELD_TYPES = array(
					"textbox" => 1,
					"multiline_textbox" => 2,
					"checkbox" => 3,
					"checkbox_list" => 4,
					"radiobutton_list" => 5,
					"dropdown_list" => 6,
					"listbox" => 7,
					"file" => 8,
					"image" => 9,
					"date" => 10,
					"audio" => 11,
					'color_picker' => 12,
					'slider' => 13,
					'related_type' => 14
					);

// Field Types
global $STANDARD_FIELDS;
$STANDARD_FIELDS = array();

// Standard fields
$STANDARD_FIELDS[12] = new PanelFields(12, 'Post/Page', array('postdivrich'), true, false, true, true, 1000);
$STANDARD_FIELDS[2] = new PanelFields(2, 'Categories', array('categorydiv'), false, false, true, false, 1000);
$STANDARD_FIELDS[14] = new PanelFields(14, 'Tags', array('tagsdiv'), true, false, true, false, 1000);

// Common advanced fields

$STANDARD_FIELDS[11] = new PanelFields(11, 'Custom Fields', array('postcustom', 'pagepostcustom', 'pagecustomdiv'), true, true, true, true, 1000);
$STANDARD_FIELDS[3] = new PanelFields(3, 'Comments & Pings', array('commentstatusdiv', 'pagecommentstatusdiv'), true, true, true, true, 1000);
$STANDARD_FIELDS[4] = new PanelFields(4, 'Password', array('passworddiv', 'pagepassworddiv'), true, true, true, true, 1000);
$STANDARD_FIELDS[18] = new PanelFields(4, 'Post/Page Author', array('authordiv', 'pageauthordiv'), true, true, true, true, 1000);

// Post-specific advanced fields
$STANDARD_FIELDS[9] = new PanelFields(9, 'Excerpt', array('postexcerpt'), true, true, true, false, 1000);
$STANDARD_FIELDS[10] = new PanelFields(10, 'Trackbacks', array('trackbacksdiv'), true, true, true, false, 1000);
$STANDARD_FIELDS[5] = new PanelFields(5, 'Post Slug', array('slugdiv'), true, true, true, false, 1000);

// Page-specific advanced fields
$STANDARD_FIELDS[15] = new PanelFields(15, 'Page Parent', array('pageparentdiv'), true, true, false, true, 1000);
$STANDARD_FIELDS[16] = new PanelFields(16, 'Page Template', array('pagetemplatediv'), true, true, false, true, 1000);
$STANDARD_FIELDS[17] = new PanelFields(17, 'Page Order', array('pageorderdiv'), true, true, false, true, 1000);										




// Important folders

// files of magic fields is wp-content/files_mf/
define('MF_FILES_NAME','files_mf');

if($is_wordpress_mu){
	$current_site = get_current_site();	
	$path_content = str_replace(DIRECTORY_SEPARATOR."mu-plugins".DIRECTORY_SEPARATOR.MF_PLUGIN_DIR,"",MF_PATH);
	$path_content = $path_content.DIRECTORY_SEPARATOR."blogs.dir".DIRECTORY_SEPARATOR.$blog_id;
}else{
	$path_content= str_replace(DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR.MF_PLUGIN_DIR,"",MF_PATH);
}

define('MF_FILES_PATH', $path_content.DIRECTORY_SEPARATOR.MF_FILES_NAME.DIRECTORY_SEPARATOR);

if($is_wordpress_mu){
	define('MF_FILES_URI',WP_CONTENT_URL.DIRECTORY_SEPARATOR."blogs.dir".DIRECTORY_SEPARATOR.$blog_id.DIRECTORY_SEPARATOR.MF_FILES_NAME.DIRECTORY_SEPARATOR);

}else{
	define('MF_FILES_URI', WP_CONTENT_URL."/".MF_FILES_NAME."/");
}
define('MF_UPLOAD_FILES_DIR', MF_FILES_PATH);
define('MF_IMAGES_CACHE_DIR', MF_FILES_PATH.'phpthumbcache'.DIRECTORY_SEPARATOR);

// Capabilities names
define('MF_CAPABILITY_PANELS', "Create Magic Fields Panels");
