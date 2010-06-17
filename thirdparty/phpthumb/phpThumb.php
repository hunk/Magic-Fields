<?php
ob_start();
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
ob_end_clean();

$MFthumb = MF_PATH.'/MF_thumb.php';
require_once($MFthumb);

//Default Values
$default = array(
					'zc'=> 1,
					'q'	=>  95,
					'w'	=>  0,
					'h'	=> 0,
					'src' => ''
				);
				
//getting the name of the image
preg_match('/\/files_mf\/([0-9\_a-z\-]+\.(jpg|png|gif))/i',$_GET['src'],$match);
$image_name_clean = $match[1];
$extension = $match[2];

//Getting the original size of the image
$file = MF_UPLOAD_FILES_DIR.$image_name_clean;
if(file_exists($file) && (empty($_GET['w']) || empty($_GET['h']))){
	$size = @getimagesize(MF_UPLOAD_FILES_DIR.$image_name_clean);
	$default['w'] = $size[0];
	$default['h'] = $size[1];
}
//TODO: sanitize the variables
$params = array();				
foreach($_GET as $key => $value){
	if(in_array($key,array('zc','w','h','q','src'))){
		$params[$key] = $value;
	}
}


$params = array_merge($default,$params);
$md5_params =  md5("w=".$params['w']."&h=".$params['h']."&q=".$params['q']."&zc=".$params['zc']);


//The file must be "jpg" or "png" or "gif" 
if(!in_array($extension,array('jpg','png','gif'))){
	return false;
}

//name with a png extension
$image_name = $md5_params."_".$image_name_clean;
//this code can be refactored
if(file_exists(MF_CACHE_DIR.$image_name)){
	//Displaying the image
	$size = getimagesize(MF_CACHE_DIR.$image_name);
	$handle = fopen(MF_CACHE_DIR.$image_name, "rb");
	$contents = NULL;
	while (!feof($handle)) {
		$contents .= fread($handle, 1024);
	}
	fclose($handle);
	
	header("Cache-Control: public"); 
	header ("Content-type: image/".$extension); 
	header("Content-Disposition: inline; filename=\"".MF_CACHE_DIR.$image_name."\""); 
	header('Content-Length: ' . filesize(MF_CACHE_DIR.$image_name)); 
	echo $contents;
}else{
	//generating the image
	$thumb = new mfthumb();
	$thumb_path = $thumb->image_resize(MF_UPLOAD_FILES_DIR.$image_name_clean,$params['w'],$params['h'],$params['zc'],MF_CACHE_DIR.$image_name);
	//Displaying the image
	if(file_exists($thumb_path)){
		$size = getimagesize($thumb_path);
		$handle = fopen($thumb_path, "rb");
		$contents = NULL;
		while (!feof($handle)) {
			$contents .= fread($handle, filesize($thumb_path));
		}
		fclose($handle);

		header("Cache-Control: public"); 
		header ("Content-type: image/".$extension); 
		header("Content-Disposition: inline; filename=\"".$thumb_path."\""); 
		header('Content-Length: ' . filesize($thumb_path)); 
		echo $contents;
	}
}
?>
