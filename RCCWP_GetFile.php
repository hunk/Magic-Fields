<?php

// use wp-load. Normally right here, but if it's not...
if( file_exists('../../../wp-load.php') )
{
	require_once('../../../wp-load.php');
	$loaded = true;
} // ...then look over here
elseif( file_exists('./mf-config.php') )
{
	include_once('./mf-config.php');
	require_once(MF_WP_LOAD);
	$loaded = true;
}

if( $loaded !== true ){
	die('Could not load wp-load.php, edit/add mf-config.php and define MF_WP_LOAD to point to a valid wp-load file.');
}


/**
 * Get the file from the web
 *
 */
function DownloadFile(){	
	global $mf_domain,  $wpdb;
	$url = $_POST['upload_url'];

	$allowedExtensions = array("pdf", "doc", "xls", "ppt", "txt", "jpeg", "psd", "jpg", "gif", "png", "docx", "pptx", "xslx", "pps", "zip", "gz", "gzip", "mp3", "aac", "mp4", "wav", "wma", "aif", "aiff", "ogg", "flv", "f4v", "mov", "avi", "mkv", "xvid", "divx","gpx");
	$path = pathinfo($url);
	$ext = $path['extension'];

	if(!in_array(strtolower($ext), $allowedExtensions)){
		echo json_encode(
        array(
        	'success'=>false,
            'error' => _("Invalid file",$mf_domain)
            )
        );
    die;
	}

	//Retrieve file
	if ($fp_source = @fopen($url, 'rb')) {
		//Get target filename
		$exploded_url = explode('.', $url);
		$ext = array_pop( $exploded_url );	
		$filename = time() . '_' . str_replace( 'rc_cwp_meta_', '', $_POST["input_name"]) . '.' . $ext;
			
		$directory = MF_FILES_PATH;
		
		$fp_dest = @fopen($directory . $filename,"wb");
		if ($fp_dest == false) return false;
		while(!feof($fp_source)) {
			set_time_limit(30);	
			$readData = fread($fp_source, 1024*2);
			fwrite($fp_dest,$readData);	
		}
		fclose($fp_source);
		fclose($fp_dest);
		
		return $filename;
	}
	return false;
}

global $mf_domain;

if ( ( isset($_SERVER['HTTPS']) && 'on' == strtolower($_SERVER['HTTPS']) ) && empty($_COOKIE[SECURE_AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
	$_COOKIE[SECURE_AUTH_COOKIE] = $_REQUEST['auth_cookie'];
elseif ( empty($_COOKIE[AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
	$_COOKIE[AUTH_COOKIE] = $_REQUEST['auth_cookie'];
	unset($current_user);

if( !( is_user_logged_in() && current_user_can('upload_files') ) ) {
    echo json_encode(
        array(
        	'success'=>false,
            'error' => _("You don't have permission to upload files, contact to the administrator for more information!",$mf_domain)
            )
        );
    die;
}

if (!empty($_POST['upload_url'])) {

  $nonce=$_POST['nonce'];
  if (! wp_verify_nonce($nonce, 'nonce_url_file') ){
  	$result = array('success'=>false,'error' => 'Sorry, your nonce did not verify.');
  	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
  	die; 
  }
	// file was send from browser 
	$_POST['upload_url'] = esc_url($_POST['upload_url']);
	$filename = DownloadFile();

	if ($filename ==  false) {
		$result_msg = '<span class="mf-upload-error">'.__("Upload Unsuccessful",$mf_domain).'!</span>';
	} else {
		$result_msg = '<span class="mf-upload-success">'.__("Successful upload",$mf_domain).'!</span>' ;
	}
	
	if($filename){	
		echo json_encode(array('success'=>true, 'msg' => $result_msg."*".$filename));
	}else{
		echo json_encode(array('success'=>true, 'msg' => $result_msg."*"."None"));
	}
}