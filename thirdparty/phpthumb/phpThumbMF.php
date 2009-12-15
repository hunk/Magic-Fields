<?php
//use wp-load. Normally right here, but if it's not...
if( file_exists('../../../../../wp-load.php')){
	require_once('../../../../../wp-load.php');
	$loaded = true;
} elseif( file_exists('./mf_config.php')){
	include_once('./mf-config.php');
	require_once(MF_WP_LOAD);
	$loaded = true;
}

if($loaded  !== true){
	die('Could not load wp-load.php, edit/add mf-config.php and define MF_WP_LOAD to point to a valid wp-load file');
}

$MFthumb = MF_PATH.'/MF_thumb.php';
require_once($MFthumb);

//Default Values
$default = array(
					'zc'=> 0,
					'w'	=> 0,
					'h'	=> 0,
					'q'	=>  85,
					'src' => ''
				);
				
//TODO: sanitize the variables
$params = array();				
foreach($_GET as $key => $value){
	if(in_array($key,array('zc','w','h','q','src'))){
		$params[$key] = $value;
	}
}

$params = array_merge($default,$params);
$md5_params =  md5("w=".$params['w']."&h=".$params['h']."&q=".$params['q']."&zc=".$params['zc']);

//Checking if already exists the image
//getting the name of the image
preg_match('/\/files_mf\/([0-9]+\.(jpg|png|jpg)|gif)/',$params['src'],$match);
$image_name_clean = $match[1];

//name with a png extension
$image_name_without_extension = preg_replace('/[a-z\.]{4}$/','',$image_name_clean);

$image_name = $md5_params."_".$image_name_without_extension;

if(file_exists(MF_UPLOAD_FILES_DIR.$image_name.".png")){
	print_r("la imagen existe");
}else{
	//generating the image
	$thumb = new mfthumb;
	$thumb->generate(MF_UPLOAD_FILES_DIR.$image_name_clean,MF_UPLOAD_FILES_DIR,$image_name,$params);
	//TODO: The mime type is hardcoded we need make a way to get the mimetype
	//'image/jpeg'
}
?>