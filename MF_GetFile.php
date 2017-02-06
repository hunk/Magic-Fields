<?php

class MF_GetFile {

	/**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_ajax_mf_get_file', array( $this, 'getFile' ) );
    }

	function getFile() {

		global $mf_domain;

		check_ajax_referer( 'nonce_url_file', 'nonce_url_file');

		if( !( is_user_logged_in() && current_user_can('upload_files') ) ) {
    		echo json_encode(
        		array(
        			'success' => false,
            		'error' => "You don't have permission to upload files, contact to the administrator for more information!",$mf_domain
            	)
        	);
    		wp_die();
		}

		if ( !isset($_POST['upload_url']) && empty($_POST['upload_url']) ) {
			echo json_encode(
        		array(
        			'success' => false,
            		'error' => __("Url missing or empty",$mf_domain)
            	)
        	);
    		wp_die();
		}

		if (!$this->isValidUrl($_POST['upload_url'])) {
			echo json_encode(
        		array(
        			'success' => false,
            		'error' => __("not a valid url format",$mf_domain)
            	)
        	);
    		wp_die();
		}

		if ( ( isset($_SERVER['HTTPS']) && 'on' == strtolower($_SERVER['HTTPS']) ) && empty($_COOKIE[SECURE_AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
			$_COOKIE[SECURE_AUTH_COOKIE] = $_REQUEST['auth_cookie'];
		elseif ( empty($_COOKIE[AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
			$_COOKIE[AUTH_COOKIE] = $_REQUEST['auth_cookie'];

		// file was send from browser 
		$_POST['upload_url'] = esc_url($_POST['upload_url']);
		$filename = $this->downloadFile();

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

		wp_die();
	}

	function isValidUrl($url) {

	    $check = preg_replace(
  			'#((https?|ftp)://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)#i',
  			"'<a href=\"$1\" target=\"_blank\">$3</a>$4'",
  			$url
		);

		return $check;
	}

	function downloadFile(){	
		global $mf_domain,  $wpdb;
		$url = $_POST['upload_url'];

		$allowedExtensions = array("pdf", "doc", "xls", "ppt", "txt", "jpeg", "psd", "jpg", "gif", "png", "docx", "pptx", "xslx", "pps", "zip", "gz", "gzip", "mp3", "aac", "mp4", "wav", "wma", "aif", "aiff", "ogg", "flv", "f4v", "mov", "avi", "mkv", "xvid", "divx","gpx");
		$path = pathinfo($url);
		$ext = $path['extension'];

		if(!in_array(strtolower($ext), $allowedExtensions)){
			echo json_encode(
        		array(
        			'success'=>false,
            		'error' => _("Invalid file extension",$mf_domain)
            	)
        	);
    		wp_die();
		}

		//Retrieve file
		if ($fp_source = @fopen($url, 'rb')) {
			//Get target filename
			$exploded_url = explode('.', $url);
			$ext = array_pop( $exploded_url );
			$input_name = filter_var($_POST["input_name"], FILTER_SANITIZE_SPECIAL_CHARS);
			$filename = time() . '_' . str_replace( 'rc_cwp_meta_', '', $input_name) . '.' . $ext;
				
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
}

$mf_get_file = new MF_GetFile();

