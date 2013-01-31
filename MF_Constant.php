<?php

global $wpdb,$is_wordpress_mu,$blog_id,$wp_version;

if (!defined('DIRECTORY_SEPARATOR'))
{
	if (strpos(php_uname('s'), 'Win') !== false )
		define('DIRECTORY_SEPARATOR', '\\');
	else 
		define('DIRECTORY_SEPARATOR', '/');
}

// General Constants
define('RC_CWP_DB_VERSION', 8);
define('RC_CWP_POST_WRITE_PANEL_ID_META_KEY', '_mf_write_panel_id');
define('RC_CWP_OPTION_KEY', 'mf_custom_write_panel');

// Magic Fields paths
preg_match('/wp-content(.*)(MF_Constant\.php)$/',__FILE__,$mfpath);
$mfpath = str_replace('\\', '/', $mfpath);
define('MF_PLUGIN_DIR', dirname(plugin_basename(__FILE__))); 
define("MF_PATH", dirname(__FILE__));

define("MF_URI", plugin_dir_url(__FILE__));
define("MF_URI_RELATIVE", 'wp-content'.$mfpath[1]);
define("PHPTHUMB",MF_URI."thirdparty/phpthumb/phpThumb.php");

//if(!is_wp30()) {
  //prefix all tables
  if(isset($current_blog)){
  	$mf_prefix=$wpdb->base_prefix;
  }else{
  	$mf_prefix=$wpdb->prefix;
  }
/*}else{
  $mf_prefix = $wpdb->get_blog_prefix();
}*/
// -- Tables names

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
// Post Type table
define('MF_TABLE_POSTTYPES_TAXONOMIES', $wpdb->prefix. 'mf_posttypes_taxonomies');

// Field Types
/*
 * @todo : This Global variable should be deprecated, we need make sure of this var is not longer used
 */
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
					'related_type' => 14,
					'markdown_textbox' => 15,
					'Image (Upload Media)' => 16
					);


// Magic Fields Field Types
/**
 * @todo : The  keys 'has_options', 'has_properties' and 'allow_multiple_values'  are really neccesary?
 */
global $mf_field_types;
$mf_field_types = array(
	1	=> array(
		'id'					=> 1,
		'name'					=> 'Textbox',
		'description'			=>	'',
		'has_options'			=>	'false',
		'has_properties'		=>	'true',
		'allow_multiple_values'	=>	'false' 
	),
	2	=> array(
		'id'					=> 2,
		'name'					=> 'Multiline Textbox',
		'description'			=>	'',
		'has_options'			=>	'false',
		'has_properties'		=>	'true',
		'allow_multiple_values'	=>	'false' 
	),
	3	=> array(
		'id'					=> 3,
		'name'					=> 'Checkbox',
		'description'			=>	'',
		'has_options'			=>	'false',
		'has_properties'		=>	'false',
		'allow_multiple_values'	=>	'false' 
	),
	4	=> array(
		'id'					=> 4,
		'name'					=> 'Checkbox List',
		'description'			=>	'',
		'has_options'			=>	'true',
		'has_properties'		=>	'false',
		'allow_multiple_values'	=>	'true' 
	),
	5	=> array(
		'id'					=> 5,
		'name'					=> 'Radiobutton List',
		'description'			=>	'',
		'has_options'			=>	'true',
		'has_properties'		=>	'false',
		'allow_multiple_values'	=>	'false' 
	),
	6	=> array(
		'id'					=> 6,
		'name'					=> 'Dropdown List',
		'description'			=>	'',
		'has_options'			=>	'true',
		'has_properties'		=>	'false',
		'allow_multiple_values'	=>	'false' 
	),
	7	=> array(
		'id'					=> 7,
		'name'					=> 'Listbox',
		'description'			=>	'',
		'has_options'			=>	'true',
		'has_properties'		=>	'true',
		'allow_multiple_values'	=>	'true' 
	),
	8	=> array(
		'id'					=> 8,
		'name'					=> 'File',
		'description'			=>	'',
		'has_options'			=>	'false',
		'has_properties'		=>	'false',
		'allow_multiple_values'	=>	'false' 
	),
	9	=> array(
		'id'					=> 9,
		'name'					=> 'Image',
		'description'			=>	'',
		'has_options'			=>	'false',
		'has_properties'		=>	'true',
		'allow_multiple_values'	=>	'false' 
	),
	10	=> array(
		'id'					=> 10,
		'name'					=> 'Date',
		'description'			=>	'',
		'has_options'			=>	'false',
		'has_properties'		=>	'true',
		'allow_multiple_values'	=>	'false' 
	),
	11	=> array(
		'id'					=> 11,
		'name'					=> 'Audio',
		'description'			=>	'',
		'has_options'			=>	'false',
		'has_properties'		=>	'false',
		'allow_multiple_values'	=>	'false' 
	),
	12	=> array(
		'id'					=> 12,
		'name'					=> 'Color Picker',
		'description'			=>	'',
		'has_options'			=>	'false',
		'has_properties'		=>	'false',
		'allow_multiple_values'	=>	'false' 
	),
	13	=> array(
		'id'					=> 13,
		'name'					=> 'Slider',
		'description'			=>	'',
		'has_options'			=>	'false',
		'has_properties'		=>	'true',
		'allow_multiple_values'	=>	'false' 
	),
	14	=> array(
		'id'					=> 14,
		'name'					=> 'Related Type',
		'description'			=>	'',
		'has_options'			=>	'false',
		'has_properties'		=>	'true',
		'allow_multiple_values'	=>	'false' 
	),
	15	=> array(
		'id'					=> 15,
		'name'					=> 'Markdown Textbox',
		'description'			=>	'',
		'has_options'			=>	'false',
		'has_properties'		=>	'false',
		'allow_multiple_values'	=>	'false' 
	),
	16	=> array(
		'id'					=> 16,
		'name'					=> 'Image (Upload Media)',
		'description'			=>	'',
		'has_options'			=>	'false',
		'has_properties'		=>	'true',
		'allow_multiple_values'	=>	'false' 
	)
);




// Field Types
global $STANDARD_FIELDS;
$STANDARD_FIELDS = array();

// Standard fields
$STANDARD_FIELDS[12] = new PanelFields(12, 'Post/Page', array('postdivrich'), true, false, true, true, 1000);
$STANDARD_FIELDS[2] = new PanelFields(2, 'Categories', array('categorydiv'), false, false, true, false, 1000);
$STANDARD_FIELDS[14] = new PanelFields(14, 'Post Tags', array('tagsdiv-post_tag'), true, false, true, false, 1000);

// Common advanced fields

$STANDARD_FIELDS[11] = new PanelFields(11, 'Custom Fields', array('postcustom', 'pagepostcustom', 'pagecustomdiv'), true, true, true, true, 1000);
$STANDARD_FIELDS[3] = new PanelFields(3, 'Discussion', array('commentstatusdiv', 'pagecommentstatusdiv'), true, true, true, true, 1000);

$STANDARD_FIELDS[18] = new PanelFields(4, 'Post/Page Author', array('authordiv', 'pageauthordiv'), true, true, true, true, 1000);

// Post-specific advanced fields
$STANDARD_FIELDS[9] = new PanelFields(9, 'Excerpt', array('postexcerpt'), true, true, true, false, 1000);
$STANDARD_FIELDS[10] = new PanelFields(10, 'Trackbacks', array('trackbacksdiv'), true, true, true, false, 1000);
$STANDARD_FIELDS[5] = new PanelFields(5, 'Post Slug', array('slugdiv'), true, true, true, false, 1000);

// Page-specific advanced fields
$STANDARD_FIELDS[15] = new PanelFields(15, 'Page Attributes', array('pageparentdiv'), true, true, false, true, 1000);
$STANDARD_FIELDS[16] = new PanelFields(16, 'Page Slug', array('pageslugdiv'), true, true, false, true, 1000);
$STANDARD_FIELDS[17] = new PanelFields(16, 'Page Revisions', array('revisionsdiv'), true, true, false, true, 1000);
									




// Important folders

// files of magic fields is wp-content/files_mf/
define('MF_FILES_NAME','files_mf');
define('MF_CACHE_NAME','cache');

if($is_wordpress_mu){
	$current_site = get_current_site();
	
	//check if WP3.0 is multisit
	if(substr($wp_version, 0, 3) < 3.0){
	  $path_content = str_replace(DIRECTORY_SEPARATOR."mu-plugins".DIRECTORY_SEPARATOR.MF_PLUGIN_DIR,"",MF_PATH);
  }else{
    $path_content = str_replace(DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR.MF_PLUGIN_DIR,"",MF_PATH);
  }
	$path_content = $path_content.DIRECTORY_SEPARATOR."blogs.dir".DIRECTORY_SEPARATOR.$blog_id;
}else{
	$path_content= str_replace(DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR.MF_PLUGIN_DIR,"",MF_PATH);
}

define('MF_FILES_PATH', $path_content.DIRECTORY_SEPARATOR.MF_FILES_NAME.DIRECTORY_SEPARATOR);
define('MF_WPCONTENT', $path_content.DIRECTORY_SEPARATOR);
if($is_wordpress_mu){
	define('MF_FILES_URI',WP_CONTENT_URL."/"."blogs.dir"."/".$blog_id."/".MF_FILES_NAME."/");
  define('MF_CACHE_URI',WP_CONTENT_URL."/"."blogs.dir"."/".$blog_id."/".MF_FILES_NAME."/".MF_CACHE_NAME."/");
}else{
	define('MF_FILES_URI', WP_CONTENT_URL."/".MF_FILES_NAME."/");
	define('MF_CACHE_URI', WP_CONTENT_URL."/".MF_FILES_NAME."/".MF_CACHE_NAME."/");
}
define('MF_UPLOAD_FILES_DIR', MF_FILES_PATH);
define('MF_CACHE_DIR', MF_FILES_PATH . MF_CACHE_NAME . DIRECTORY_SEPARATOR);

// Define Cache Bool and Dir
// Conditionals for overriding on per site basis without touching this file
if( !defined( "MF_GET_CACHE_IS_ON" ) ) { define("MF_GET_CACHE_IS_ON", FALSE );}  //By Default the cache is false
if( !defined( "MF_GET_CACHE_DIR" ) ) { define("MF_GET_CACHE_DIR", MF_FILES_PATH. 'fields_cache/' ); }
elseif(!file_exists( MF_GET_CACHE_DIR ) ) {
	wp_die( 'Caching folder '. MF_GET_CACHE_DIR . 'you&rsquo;ve set up does&rsquo;t exist.','Caching Magic Fields');
}

// Capabilities names
define('MF_CAPABILITY_PANELS', "Create Magic Fields Panels");


//return TRUE is WP version >= 3.0
function is_wp30(){
	global $wp_version;
	
	if(substr($wp_version, 0, 3) >= 3.0)
		return TRUE;
	 
	return FALSE;
}

//return TRUE is WP version >= 3.1
function is_wp31(){
	global $wp_version;
	
	if(substr($wp_version, 0, 3) >= 3.1)
		return TRUE;
	 
	return FALSE;
}
