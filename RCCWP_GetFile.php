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
	$url = $_POST['upload_url'];
	if ('1' == $_POST['type']){
		$acceptedExts = "image";
	}elseif ('2' == $_POST['type']){
		$acceptedExts = "audio"; 
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

if (!(is_user_logged_in() &&
      (current_user_can('edit_posts') || current_user_can('edit_published_pages'))))
	die(__("Athentication failed!",$mf_domain));

if (!empty($_POST['upload_url'])) {

  $nonce=$_POST['nonce'];
  if (! wp_verify_nonce($nonce, 'nonce_url_file') ) die('Sorry, your nonce did not verify.'); 
	// file was send from browser 
	$_POST['upload_url'] = esc_url($_POST['upload_url']);
	$filename = DownloadFile();

	if ($filename ==  false) {			
		$result_msg = '<span class="mf-upload-error">'.__("Upload Unsuccessful",$mf_domain).'!</span>';
	} else {
		$result_msg = '<span class="mf-upload-success">'.__("Successful upload",$mf_domain).'!</span>' ;
		$operationSuccess = "true";
	}
	
	if($filename){	
		echo $result_msg."*".$filename;
	}else{
		echo $result_msg."*"."None";
	}
}
